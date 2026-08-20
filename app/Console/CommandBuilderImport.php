<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use SkillDo\Cms\Element\ElementBuilder;
use SkillDo\Cms\Models\ElementBuilderSection;
use SkillDo\Cms\Models\ElementBuilderTemplate;
use Theme\Builders\ThemeFooter;
use Theme\Builders\ThemeHeader;
use Theme\Builders\ThemeHome;

class CommandBuilderImport extends Command
{
    protected string $signature = 'builder:import {section} {file?} {--no-backup}';

    protected string $description = 'Import builder section (header, home, footer) from JSON file';

    public function handle(): bool
    {
        $section = $this->argument('section');

        $allowedSections = ['header', 'home', 'footer'];

        if (!in_array($section, $allowedSections))
        {
            $this->line('Error: Section must be one of: ' . implode(', ', $allowedSections));
            return self::ERROR;
        }

        // Resolve input file path
        $file = $this->argument('file');

        $filePath = app('path.storage') . '/cms/builder/' . ($file ?? $section . '.json');

        if (!file_exists($filePath))
        {
            $this->line('Error: File not found: ' . $filePath);
            return self::ERROR;
        }

        $json = file_get_contents($filePath);

        $rows = json_decode($json, true);

        if (!is_array($rows))
        {
            $this->line('Error: Invalid JSON data. Must be a valid array.');
            return self::ERROR;
        }

        // Backup (unless --no-backup)
        $noBackup = $this->option('no-backup');

        if (!$noBackup)
        {
            $pageData = ElementBuilder::page($section);

            if (!empty($pageData) && !empty($pageData['sections'][$section]))
            {
                $currentSetting = $pageData['sections'][$section]->setting;

                if (!empty($currentSetting))
                {
                    $backupDir = app('path.storage') . '/cms/builder/backups/';

                    if (!is_dir($backupDir)) {
                        mkdir($backupDir, 0755, true);
                    }

                    $backupFile = $backupDir . $section . '_backup_' . date('Y-m-d_His') . '.json';

                    $backupJson = json_encode($currentSetting, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    file_put_contents($backupFile, $backupJson);

                    $this->line('Backup saved to: ' . $backupFile, 'blue');
                }
            }
        }

        // Check template and section exist
        $template = ElementBuilderTemplate::where('key', $section)->first();

        if (empty($template))
        {
            $this->line('Error: ElementBuilderTemplate with key "' . $section . '" not found. Please create it in the admin panel first.');
            return self::ERROR;
        }

        $sectionRecord = ElementBuilderSection::where('key', $section)->first();

        if (empty($sectionRecord))
        {
            $this->line('Error: ElementBuilderSection with key "' . $section . '" not found. Please create it in the admin panel first.');
            return self::ERROR;
        }

        // Process each row
        foreach ($rows as &$row)
        {
            if (!empty($row['columns']) && is_array($row['columns']))
            {
                ElementBuilder::handleSectionData($row['columns']);
            }
        }

        unset($row);

        // Save section setting
        ElementBuilder::saveSectionSetting($section, $rows);

        $this->line('Data imported for section "' . $section . '"', 'green');

        // Build
        try {
            match ($section)
            {
                'header' => ThemeHeader::build(),
                'home'   => ThemeHome::build(),
                'footer' => ThemeFooter::build(),
            };

            $this->line('Build completed successfully!', 'green');
        }
        catch (\Throwable $e)
        {
            $this->line('Warning: Build failed — ' . $e->getMessage(), 'yellow');
            $this->line('Data was imported but the build step encountered an error.');
        }

        return self::SUCCESS;
    }
}


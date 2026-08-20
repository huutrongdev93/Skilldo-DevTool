<?php
namespace DevTool\Console;

use DevTool\Commands\Command;
use SkillDo\Cms\Element\ElementBuilder;

class CommandBuilderExport extends Command
{
    protected string $signature = 'builder:export {section} {file?}';

    protected string $description = 'Export builder section (header, home, footer) to JSON file';

    public function handle(): bool
    {
        $section = $this->argument('section');

        $allowedSections = ['header', 'home', 'footer'];

        if (!in_array($section, $allowedSections))
        {
            $this->line('Error: Section must be one of: ' . implode(', ', $allowedSections));
            return self::ERROR;
        }

        // Load page data
        $pageData = ElementBuilder::page($section);

        if (empty($pageData))
        {
            $this->line('Error: No data found for section "' . $section . '"');
            return self::ERROR;
        }

        if (empty($pageData['sections'][$section]))
        {
            $this->line('Error: Section "' . $section . '" not found in page data');
            return self::ERROR;
        }

        $rows = $pageData['sections'][$section]->setting;

        if (empty($rows))
        {
            $this->line('Error: Section "' . $section . '" has no setting data');
            return self::ERROR;
        }

        // Resolve output path
        $dir = app('path.storage') . '/cms/builder/';

        if (!is_dir($dir))
        {
            mkdir($dir, 0755, true);
        }

        $file = $this->argument('file');

        $filePath = $dir . ($file ?? $section . '.json');

        // Write JSON
        $json = json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        file_put_contents($filePath, $json);

        $this->line('Exported successfully to: ' . $filePath, 'green');

        return self::SUCCESS;
    }
}


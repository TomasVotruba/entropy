<?php

declare(strict_types=1);

namespace Entropy\Tests\Console\ConsoleTable;

use Entropy\Console\ConsoleTable\ConsoleTable;
use Entropy\Console\ConsoleTable\ValueObject\TableRow;
use Entropy\Console\ConsoleTable\ValueObject\TableView;
use Entropy\Console\ConsoleTable\ViewRenderer;
use Entropy\Console\Output\OutputColorizer;
use Entropy\Console\Output\OutputPrinter;
use PHPUnit\Framework\TestCase;

final class ViewRendererTest extends TestCase
{
    private ViewRenderer $viewRenderer;

    protected function setUp(): void
    {
        $outputPrinter = new OutputPrinter(new OutputColorizer());
        $this->viewRenderer = new ViewRenderer($outputPrinter, new ConsoleTable($outputPrinter));
    }

    public function testRenderPlainView(): void
    {
        $tableView = new TableView('Files', 'Count', [
            new TableRow('Directories', '12', null, false),
            new TableRow('Files', '345', null, false),
        ]);

        $this->viewRenderer->renderTableView($tableView);

        // no exception is thrown while rendering through the silent printer
        $this->expectNotToPerformAssertions();
    }

    public function testRenderViewWithRelativeAndChildRows(): void
    {
        $tableView = new TableView('Languages', 'Lines', [
            new TableRow('PHP', '1000', '80.0', false),
            new TableRow('src', '800', '64.0', true),
            new TableRow('YAML', '250', '20.0', false),
        ], true);

        $this->viewRenderer->renderTableView($tableView);

        $this->expectNotToPerformAssertions();
    }
}

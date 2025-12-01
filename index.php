<?php

require 'class/Sequence.php';
require 'class/Queue.php';
require 'class/Node.php';
require 'class/Stack.php';
require 'class/Graph.php';
require 'class/Walker.php';
require 'class/Dijkstra.php';

/**
 * CLI Helpers
 */
function input(string $label): string {
    echo "$label ";
    return trim(readline());
}

function color(string $text, string $code): string {
    return "\033[" . $code . "m" . $text . "\033[0m";
}

function green($text) { return color($text, '32'); }
function yellow($text) { return color($text, '33'); }
function cyan($text) { return color($text, '36'); }
function red($text) { return color($text, '31'); }
function bold($text) { return color($text, '1'); }

/**
 * HEADER
 */
echo bold("==============================\n");
echo bold("   GRAPH & DIJKSTRA DEMO\n");
echo bold("==============================\n");
echo "1) " . cyan("Load Knight moves 8×8 graph") . "\n";
echo "2) " . cyan("Load predefined A–G weighted graph") . "\n";
echo "3) " . red("Exit") . "\n\n";

$choice = input("Choose option (1/2/3):");

switch ($choice) {
    case "1":
        $graph = buildKnightGraph();
        break;

    case "2":
        $graph = buildSampleGraph();
        break;

    case "3":
        echo yellow("Exiting...\n");
        exit;

    default:
        echo red("Invalid option.\n");
        exit;
}

/**
 * SHOW LOADED NODES
 */
echo "\n" . bold("Available Nodes:\n");

$nodes = iterator_to_array($graph->getNodes());
sort($nodes);

echo cyan(implode(', ', $nodes)) . "\n\n";

/**
 * ASK START/END NODES
 */
$start = input("Enter START node:");
$end   = input("Enter END node:");

if (!in_array($start, $nodes)) {
    echo red("Node '$start' does not exist in graph.\n");
    exit;
}
if (!in_array($end, $nodes)) {
    echo red("Node '$end' does not exist in graph.\n");
    exit;
}

$dijkstra = new Dijkstra($graph);

echo "\n" . bold("Calculating shortest path...\n");

try {
    $pathString = $dijkstra->getShortestPath($start, $end);

    // Convert "563523" into ["56", "35", "23"]
    $path = [];

    // A–G mode returns array already
    if (is_array($pathString)) {
        $path = $pathString;
    } else {
        // Knight graph path comes as string — convert properly
        for ($i = 0; $i < strlen($pathString); $i += 2) {
            $path[] = substr($pathString, $i, 2);
        }
    }

    echo green("\nShortest Path from $start to $end:\n");

    echo bold(implode(" → ", $path)) . "\n";

} catch (Exception $e) {
    echo red("Error: " . $e->getMessage() . "\n");
}

echo "\n" . bold("Done.\n");


/**
 * -----------------------------
 * GRAPH BUILDERS
 * -----------------------------
 */

function buildKnightGraph(): Graph
{
    echo yellow("Building 8×8 knight move graph...\n");
    $graph = new Graph();

    for ($x = 0; $x < 8; $x++)
        for ($y = 0; $y < 8; $y++)
            $graph->addNode("$x$y");

    $moves = [
        [1, 2], [2, 1], [-1, 2], [-2, 1],
        [1, -2], [2, -1], [-1, -2], [-2, -1],
    ];

    for ($x = 0; $x < 8; $x++) {
        for ($y = 0; $y < 8; $y++) {
            foreach ($moves as [$dx, $dy]) {
                $nx = $x + $dx;
                $ny = $y + $dy;

                if ($nx >= 0 && $nx < 8 && $ny >= 0 && $ny < 8) {
                    $graph->addEdge("$x$y", "$nx$ny", 1);
                }
            }
        }
    }

    return $graph;
}

function buildSampleGraph(): Graph
{
    echo yellow("Building sample weighted graph A–G...\n");

    $graph = new Graph();

    foreach (['A','B','C','D','E','F','G'] as $node) {
        $graph->addNode($node);
    }

    $graph->addEdge('A','B',2);
    $graph->addEdge('A','C',3);
    $graph->addEdge('A','D',6);

    $graph->addEdge('B','C',4);
    $graph->addEdge('B','E',9);

    $graph->addEdge('C','D',1);
    $graph->addEdge('C','E',7);
    $graph->addEdge('C','F',6);

    $graph->addEdge('D','F',4);

    $graph->addEdge('E','F',1);
    $graph->addEdge('E','G',5);

    $graph->addEdge('F','G',8);

    return $graph;
}

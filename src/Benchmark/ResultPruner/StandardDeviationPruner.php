<?php

namespace TylerSommer\Nice\Benchmark\ResultPruner;

use TylerSommer\Nice\Benchmark\ResultPruner;

class StandardDeviationPruner implements ResultPruner
{
    /**
     * @var int
     */
    private $deviations;

    /**
     * Constructor
     * 
     * @param int $deviations
     */
    public function __construct($deviations = 3)
    {
        $this->deviations = $deviations;
    }
    
    /**
     * Prune the results
     *
     * @param array $results
     * 
     * @return array The pruned results
     */
    public function prune(array $results)
    {
        $mean = array_sum($results) / count($results);
        $deviation = $this->deviations * $this->standardDeviation($results);
        $lower = $mean - $deviation;
        $upper = $mean + $deviation;
        
        return array_filter($results, function($val) use ($lower, $upper) {
                return $val >= $lower && $val <= $upper;
            });
    }

    /**
     * Returns one standard deviation for the given results
     * 
     * @param array $results
     *
     * @return float
     */
    private function standardDeviation(array $results) {
        $mean = array_sum($results) / count($results);
        $initial = 0;
        $f = function ($carry, $val) use ($mean) {
            return $carry + pow($val - $mean, 2);
        };
        $sum = array_reduce($results, $f, $initial);
        $n = count($results) - 1;
        
        return sqrt($sum / $n);
    }

    /**
     * Gets a string describing this pruner
     * 
     * @return string
     */
    public function getDescription()
    {
        return sprintf('Values that fall outside of %s standard deviations of the mean will be discarded.', $this->deviations);
    }
}
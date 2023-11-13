<?php

namespace DS\Ted3\TypoScript;


/**
 * Example condition
 */
class TestCondition extends \TYPO3\CMS\Core\Configuration\TypoScript\ConditionMatching\AbstractCondition {

    /**
     * Evaluate condition
     */
    public function matchCondition(array $conditionParameters) {
        echo "testcondition"; exit;
        return true;

    }

}

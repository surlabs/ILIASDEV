<?php
/* Copyright (c) 1998-2014 ILIAS open source, Extended GPL, see docs/LICENSE */

require_once 'Services/WorkflowEngine/test/ilWorkflowEngineBaseTest.php';

/**
 * @author Maximilian Becker <mbecker@databay.de>
 * @version $Id$
 *
 * @ingroup Services/WorkflowEngine
 */
class test_006_Task extends ilWorkflowEngineBaseTest
{
    #region Helper
    public string $base_path = './Services/WorkflowEngine/test/parser/';
    public string $suite_path = '006_Task/';

    public function getTestInputFilename($test_name) : string
    {
        return $this->base_path . $this->suite_path . $test_name . '.bpmn2';
    }

    public function getTestOutputFilename($test_name) : string
    {
        return $this->base_path . $this->suite_path . $test_name . '_output.php';
    }

    public function getTestGoldsampleFilename($test_name) : string
    {
        return $this->base_path . $this->suite_path . $test_name . '_goldsample.php';
    }

    protected function setUp() : void
    {
        chdir(__DIR__);
        chdir('../../../../../');

        parent::setUp();

        require_once './Services/WorkflowEngine/classes/parser/class.ilBPMN2Parser.php';
    }

    public function test_WorkflowWithSimpleTaskShouldOutputAccordingly() : void
    {
        $test_name = 'Task_Simple';
        $xml = file_get_contents($this->getTestInputFilename($test_name));
        $parser = new ilBPMN2Parser();
        $parse_result = $parser->parseBPMN2XML($xml);
        
        file_put_contents($this->getTestOutputFilename($test_name), $parse_result);
        $return = exec('php -l ' . $this->getTestOutputFilename($test_name));

        $this->assertEquals('No syntax errors detected', substr($return, 0, 25), 'Lint of output code failed.');

        $goldsample = file_get_contents($this->getTestGoldsampleFilename($test_name));
        $this->assertEquals($goldsample, $parse_result, 'Output does not match goldsample.');

        require_once $this->getTestOutputFilename($test_name);
        $process = new $test_name;
        $this->assertFalse($process->isActive());

        $process->startWorkflow();
        $all_triggered = true;
        foreach ($process->getNodes() as $node) {
            /** @var ilNode $node*/
            foreach ($node->getDetectors() as $detector) {
                /** @var ilSimpleDetector $detector */
                if (!$detector->getActivated()) {
                    $all_triggered = false;
                }
            }
            foreach ($node->getEmitters() as $emitter) {
                /** @var ilActivationEmitter $emitter */
                if (!$emitter->getActivated()) {
                    $all_triggered = false;
                }
            }
        }
        $this->assertTrue($all_triggered);
        unlink($this->getTestOutputFilename($test_name));
    }

    public function test_WorkflowWithSimpleCallActivityShouldOutputAccordingly() : void
    {
        $test_name = 'Task_CallActivity_Simple';
        $xml = file_get_contents($this->getTestInputFilename($test_name));
        $parser = new ilBPMN2Parser();
        $parse_result = $parser->parseBPMN2XML($xml);

        file_put_contents($this->getTestOutputFilename($test_name), $parse_result);
        $return = exec('php -l ' . $this->getTestOutputFilename($test_name));

        $this->assertEquals('No syntax errors detected', substr($return, 0, 25), 'Lint of output code failed.');

        $goldsample = file_get_contents($this->getTestGoldsampleFilename($test_name));
        $this->assertEquals($goldsample, $parse_result, 'Output does not match goldsample.');

        require_once $this->getTestOutputFilename($test_name);
        $process = new $test_name;
        self::$wasTriggered = false;
        $process->startWorkflow();
        $all_triggered = true;
        foreach ($process->getNodes() as $node) {
            /** @var ilNode $node*/
            foreach ($node->getDetectors() as $detector) {
                /** @var ilSimpleDetector $detector */
                if (!$detector->getActivated()) {
                    $all_triggered = false;
                }
            }
            foreach ($node->getEmitters() as $emitter) {
                /** @var ilActivationEmitter $emitter */
                if (!$emitter->getActivated()) {
                    $all_triggered = false;
                }
            }
        }
        $this->assertTrue($all_triggered, 'Not all nodes were triggered.');
        $this->assertTrue(self::$wasTriggered, 'Static method call was not called.');
        unlink($this->getTestOutputFilename($test_name));
    }

    public static bool $wasTriggered; // See test above.

    public static function triggerMe($context = "", $params = "") // See test above.
    : void
    {
        self::$wasTriggered = true;
    }

    public function test_WorkflowWithManualTaskShouldOutputAccordingly() : void
    {
        $test_name = 'Task_ManualTask_Simple';
        $xml = file_get_contents($this->getTestInputFilename($test_name));
        $parser = new ilBPMN2Parser();
        $parse_result = $parser->parseBPMN2XML($xml);

        file_put_contents($this->getTestOutputFilename($test_name), $parse_result);
        $return = exec('php -l ' . $this->getTestOutputFilename($test_name));

        $this->assertEquals('No syntax errors detected', substr($return, 0, 25), 'Lint of output code failed.');

        $goldsample = file_get_contents($this->getTestGoldsampleFilename($test_name));
        $this->assertEquals($goldsample, $parse_result, 'Output does not match goldsample.');

        require_once $this->getTestOutputFilename($test_name);
        $process = new $test_name;
        $process->startWorkflow();
        $all_triggered = true;
        foreach ($process->getNodes() as $node) {
            /** @var ilNode $node*/
            foreach ($node->getDetectors() as $detector) {
                /** @var ilSimpleDetector $detector */
                if (!$detector->getActivated()) {
                    $all_triggered = false;
                }
            }
            foreach ($node->getEmitters() as $emitter) {
                /** @var ilActivationEmitter $emitter */
                if (!$emitter->getActivated()) {
                    $all_triggered = false;
                }
            }
        }
        $this->assertTrue($all_triggered);

        unlink($this->getTestOutputFilename($test_name));
    }

    public function test_WorkflowWithScriptTaskShouldOutputAccordingly() : void
    {
        $test_name = 'Task_ScriptTask_Simple';
        $xml = file_get_contents($this->getTestInputFilename($test_name));
        $parser = new ilBPMN2Parser();
        $parse_result = $parser->parseBPMN2XML($xml);

        file_put_contents($this->getTestOutputFilename($test_name), $parse_result);
        $return = exec('php -l ' . $this->getTestOutputFilename($test_name));

        $this->assertEquals('No syntax errors detected', substr($return, 0, 25), 'Lint of output code failed.');

        $goldsample = file_get_contents($this->getTestGoldsampleFilename($test_name));
        $this->assertEquals($goldsample, $parse_result, 'Output does not match goldsample.');

        require_once $this->getTestOutputFilename($test_name);
        self::$wasTriggered = false;
        $process = new $test_name;
        $process->startWorkflow();
        $all_triggered = true;
        foreach ($process->getNodes() as $node) {
            /** @var ilNode $node*/
            foreach ($node->getDetectors() as $detector) {
                /** @var ilSimpleDetector $detector */
                if (!$detector->getActivated()) {
                    $all_triggered = false;
                }
            }
            foreach ($node->getEmitters() as $emitter) {
                /** @var ilActivationEmitter $emitter */
                if (!$emitter->getActivated()) {
                    $all_triggered = false;
                }
            }
        }
        $this->assertTrue($all_triggered, 'Not all nodes were triggered.');
        $this->assertTrue(self::$wasTriggered, 'Static method call was not called.');

        unlink($this->getTestOutputFilename($test_name));
    }

    public static bool $wasListRequestTriggered;
    //public static $callParams;
    public static array $retval = array(1, 2, 3, 4, 5);

    public static function requestList($context, $params) : array
    {
        self::$wasListRequestTriggered = true;
        //self::$callParams = $params;
        return array('DataObjectReference_1' => self::$retval);
    }

    public function test_ReadLearnersFromCourseShouldOutputAccordingly() : void
    {
        $test_name = 'Task_ReadLearnersFromCourse';
        $xml = file_get_contents($this->getTestInputFilename($test_name));
        $parser = new ilBPMN2Parser();
        $parse_result = $parser->parseBPMN2XML($xml);

        file_put_contents($this->getTestOutputFilename($test_name), $parse_result);
        $return = exec('php -l ' . $this->getTestOutputFilename($test_name));

        $this->assertEquals('No syntax errors detected', substr($return, 0, 25), 'Lint of output code failed.');

        $goldsample = file_get_contents($this->getTestGoldsampleFilename($test_name));
        $this->assertEquals($goldsample, $parse_result, 'Output does not match goldsample.');

        require_once $this->getTestOutputFilename($test_name);
        self::$wasListRequestTriggered = false;
        $process = new $test_name;

        $process->setInstanceVarById('DataInput_1', 123456789);
        $this->assertEquals(123456789, $process->getInstanceVarById('DataInput_1'));
        $process->startWorkflow();
        $all_triggered = true;
        foreach ($process->getNodes() as $node) {
            /** @var ilNode $node*/
            foreach ($node->getDetectors() as $detector) {
                /** @var ilSimpleDetector $detector */
                if (!$detector->getActivated()) {
                    $all_triggered = false;
                }
            }
            foreach ($node->getEmitters() as $emitter) {
                /** @var ilActivationEmitter $emitter */
                if (!$emitter->getActivated()) {
                    $all_triggered = false;
                }
            }
        }
        $this->assertTrue($all_triggered, 'Not all nodes were triggered.');
        $this->assertTrue(self::$wasListRequestTriggered, 'Static method call was not called.');
        $this->assertEquals(self::$retval, $process->getInstanceVarById('DataObjectReference_1'));

        unlink($this->getTestOutputFilename($test_name));
    }
}

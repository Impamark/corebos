<?php

/**
 * This code was generated by
 * \ / _    _  _|   _  _
 * | (_)\/(_)(_|\/| |(/_  v1.0.0
 * /       /
 */

namespace Twilio\Rest\Taskrouter\V1\Workspace;

use Twilio\Exceptions\TwilioException;
use Twilio\InstanceContext;
use Twilio\Options;
use Twilio\Rest\Taskrouter\V1\Workspace\Workflow\WorkflowCumulativeStatisticsList;
use Twilio\Rest\Taskrouter\V1\Workspace\Workflow\WorkflowRealTimeStatisticsList;
use Twilio\Rest\Taskrouter\V1\Workspace\Workflow\WorkflowStatisticsList;
use Twilio\Values;
use Twilio\Version;

/**
 * @property \Twilio\Rest\Taskrouter\V1\Workspace\Workflow\WorkflowStatisticsList statistics
 * @property \Twilio\Rest\Taskrouter\V1\Workspace\Workflow\WorkflowRealTimeStatisticsList realTimeStatistics
 * @property \Twilio\Rest\Taskrouter\V1\Workspace\Workflow\WorkflowCumulativeStatisticsList cumulativeStatistics
 * @method \Twilio\Rest\Taskrouter\V1\Workspace\Workflow\WorkflowStatisticsContext statistics()
 * @method \Twilio\Rest\Taskrouter\V1\Workspace\Workflow\WorkflowRealTimeStatisticsContext realTimeStatistics()
 * @method \Twilio\Rest\Taskrouter\V1\Workspace\Workflow\WorkflowCumulativeStatisticsContext cumulativeStatistics()
 */
class WorkflowContext extends InstanceContext {
    protected $_statistics = null;
    protected $_realTimeStatistics = null;
    protected $_cumulativeStatistics = null;

    /**
     * Initialize the WorkflowContext
     * 
     * @param \Twilio\Version $version Version that contains the resource
     * @param string $workspaceSid The workspace_sid
     * @param string $sid The sid
     * @return \Twilio\Rest\Taskrouter\V1\Workspace\WorkflowContext 
     */
    public function __construct(Version $version, $workspaceSid, $sid) {
        parent::__construct($version);

        // Path Solution
        $this->solution = array('workspaceSid' => $workspaceSid, 'sid' => $sid, );

        $this->uri = '/Workspaces/' . rawurlencode($workspaceSid) . '/Workflows/' . rawurlencode($sid) . '';
    }

    /**
     * Fetch a WorkflowInstance
     * 
     * @return WorkflowInstance Fetched WorkflowInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function fetch() {
        $params = Values::of(array());

        $payload = $this->version->fetch(
            'GET',
            $this->uri,
            $params
        );

        return new WorkflowInstance(
            $this->version,
            $payload,
            $this->solution['workspaceSid'],
            $this->solution['sid']
        );
    }

    /**
     * Update the WorkflowInstance
     * 
     * @param array|Options $options Optional Arguments
     * @return WorkflowInstance Updated WorkflowInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function update($options = array()) {
        $options = new Values($options);

        $data = Values::of(array(
            'FriendlyName' => $options['friendlyName'],
            'AssignmentCallbackUrl' => $options['assignmentCallbackUrl'],
            'FallbackAssignmentCallbackUrl' => $options['fallbackAssignmentCallbackUrl'],
            'Configuration' => $options['configuration'],
            'TaskReservationTimeout' => $options['taskReservationTimeout'],
        ));

        $payload = $this->version->update(
            'POST',
            $this->uri,
            array(),
            $data
        );

        return new WorkflowInstance(
            $this->version,
            $payload,
            $this->solution['workspaceSid'],
            $this->solution['sid']
        );
    }

    /**
     * Deletes the WorkflowInstance
     * 
     * @return boolean True if delete succeeds, false otherwise
     * @throws TwilioException When an HTTP error occurs.
     */
    public function delete() {
        return $this->version->delete('delete', $this->uri);
    }

    /**
     * Access the statistics
     * 
     * @return \Twilio\Rest\Taskrouter\V1\Workspace\Workflow\WorkflowStatisticsList 
     */
    protected function getStatistics() {
        if (!$this->_statistics) {
            $this->_statistics = new WorkflowStatisticsList(
                $this->version,
                $this->solution['workspaceSid'],
                $this->solution['sid']
            );
        }

        return $this->_statistics;
    }

    /**
     * Access the realTimeStatistics
     * 
     * @return \Twilio\Rest\Taskrouter\V1\Workspace\Workflow\WorkflowRealTimeStatisticsList 
     */
    protected function getRealTimeStatistics() {
        if (!$this->_realTimeStatistics) {
            $this->_realTimeStatistics = new WorkflowRealTimeStatisticsList(
                $this->version,
                $this->solution['workspaceSid'],
                $this->solution['sid']
            );
        }

        return $this->_realTimeStatistics;
    }

    /**
     * Access the cumulativeStatistics
     * 
     * @return \Twilio\Rest\Taskrouter\V1\Workspace\Workflow\WorkflowCumulativeStatisticsList 
     */
    protected function getCumulativeStatistics() {
        if (!$this->_cumulativeStatistics) {
            $this->_cumulativeStatistics = new WorkflowCumulativeStatisticsList(
                $this->version,
                $this->solution['workspaceSid'],
                $this->solution['sid']
            );
        }

        return $this->_cumulativeStatistics;
    }

    /**
     * Magic getter to lazy load subresources
     * 
     * @param string $name Subresource to return
     * @return \Twilio\ListResource The requested subresource
     * @throws \Twilio\Exceptions\TwilioException For unknown subresources
     */
    public function __get($name) {
        if (property_exists($this, '_' . $name)) {
            $method = 'get' . ucfirst($name);
            return $this->$method();
        }

        throw new TwilioException('Unknown subresource ' . $name);
    }

    /**
     * Magic caller to get resource contexts
     * 
     * @param string $name Resource to return
     * @param array $arguments Context parameters
     * @return \Twilio\InstanceContext The requested resource context
     * @throws \Twilio\Exceptions\TwilioException For unknown resource
     */
    public function __call($name, $arguments) {
        $property = $this->$name;
        if (method_exists($property, 'getContext')) {
            return call_user_func_array(array($property, 'getContext'), $arguments);
        }

        throw new TwilioException('Resource does not have a context');
    }

    /**
     * Provide a friendly representation
     * 
     * @return string Machine friendly representation
     */
    public function __toString() {
        $context = array();
        foreach ($this->solution as $key => $value) {
            $context[] = "$key=$value";
        }
        return '[Twilio.Taskrouter.V1.WorkflowContext ' . implode(' ', $context) . ']';
    }
}
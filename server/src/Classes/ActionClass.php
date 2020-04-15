<?php

namespace Gernzy\Server\Classes;

use Gernzy\Server\Exceptions\GernzyException;

class ActionClass
{
    protected $meta = [];
    protected $dataModified = [];
    protected $dataOriginal = [];

    public function __construct($dataOriginal = null)
    {
        $this->dataOriginal = $dataOriginal;
        $this->eventPreventDefault = false;
    }

    /**
     * Set the meta of the action object, which is the name of the actions that are configure to fire on the event.
     */
    public function setMeta($action)
    {
        array_push($this->meta, [
            'action' => $action
        ]);
    }

    /**
     * This is the main funtion used by third parties to attach data to the Action Place holder object, to be used by next package.
     * This data will be appended to the $dataModified array property of this object
     * @param string
     * @param $var
     */
    public function attachData($actionName, $data = [])
    {

        // Interface the action must implement
        $interfaces = class_implements($actionName);

        // Check if the interface exists on the class
        if (!isset($interfaces['Gernzy\Server\Services\ActionInterface'])) {
            throw new GernzyException(
                'The provided class does not implement Gernzy\Server\Services\ActionInterface.',
                'Please provide the appropriate class name.'
            );
        }

        array_push($this->dataModified, [
            'actionName' => $actionName,
            'data' => $data
        ]);
    }

    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * Returns the last element of the modified data array
     * @param string
     * @param $var
     */
    public function getLastModifiedData()
    {
        $data = end($this->dataModified);

        if (isset($data['data'])) {
            return $data['data'];
        } else {
            return $this->getAllModifiedData();
        }
    }

    /**
     * Returns the entire modified data array
     */
    public function getAllModifiedData()
    {
        return $this->dataModified;
    }

    /**
     * Returns the original data attached at the creation of the event. This data should not be modified by third parties
     */
    public function getOriginalData()
    {
        return $this->dataOriginal;
    }

    /**
     * Set the original data attached from the event
     */
    public function attachOriginalData($data)
    {
        $this->dataOriginal = $data;
    }

    public function eventPreventDefault()
    {
        $this->eventPreventDefault = true;
    }

    public function isEventPreventDefault()
    {
        return $this->eventPreventDefault;
    }
}

<?php

namespace Gernzy\Server\Services;

use Gernzy\Server\Classes\ActionClass;

class EventService
{
    public function __construct()
    {
    }

    /**
     * This is the main funtion to manage which actions should be executed
     *
     * @param string
     * @param $var
     */
    public static function triggerEvent($event, $data = [])
    {
        // Lookup the event in config, and get action to set off
        $actions = config('events.' . $event);
        if (empty($actions)) {
            return;
        }

        // This is a placeholder object that acts as a data store, as the various third parties
        // interact with the data for this event. This object will be passed along to every action that is
        // registered for the event.
        $actionDataHolder = new ActionClass();

        // Set the original data on the ActionDataHolder
        $actionDataHolder->attachOriginalData($data);

        // Loop through all the actions found, and call appropriate methods
        foreach ($actions as $action) {
            // This meta is keeping the name of each action that has interacted with the event.
            $actionDataHolder->setMeta($action);

            // Fire up the actual action
            $actionInstance = new $action();

            // Call the run function which receive the actionDataHolder and returns the modified version
            $actionDataHolder = $actionInstance->run($actionDataHolder);
        }

        // in case history of event interaction is needed
        return $actionDataHolder;
    }
}

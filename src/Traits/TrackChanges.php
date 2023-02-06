<?php

namespace SGT\Traits;

use SGT\Model\Change;
use SGT\Observer\ChangeObserver;

trait TrackChanges
{

    # Can add track_ignore_fields which will ignore particular fields from
    # being saved.

    public static function bootTrackChanges()
    {

        if (isset(self::$changeObserver))
        {
            self::observe(self::$changeObserver);
        }
        else
        {
            self::observe(ChangeObserver::class);
        }

    }

    /**
     * Quick way to add history to a model.
     * The field can be any string data you'd like, and the value
     * can be any string value also.
     *
     * @param $field
     * @param $value
     */
    public function addChangeNotice($field, $value = null)
    {

        $history = new Change();

        $history->field = $this->translateName($field);
        $history->value = $this->translateValue($field, $value);
        $history->save();

        $this->changes()->save($history);

    }

    public function addChangeNotices(array $changes)
    {

        if (count($changes) < 1)
        {
            return;
        }

        $load_changes = [];

        foreach ($changes as $field => $value)
        {

            $field_name = $this->translateName($field);

            $field_value = $this->translateValue($field, $value);

            $load_changes[] = [
                'field' => $field_name,
                'value' => $field_value
            ];

        }

        if (count($load_changes) > 0)
        {
            foreach ($load_changes as $change)
            {
                $change = Change::create($change);
                $this->changes()->save($change);
            }
        }
    }

    public function changes()
    {

        return $this->morphMany(Change::class, 'reportable');
    }

    /**
     * @param $field
     *
     * @return bool true if the passed in field should be ignored.
     */
    public function ignoreField($field)
    {

        if (property_exists($this, 'track_ignore_fields'))
        {
            return in_array($field, $this->track_ignore_fields);
        }

        return false;

    }

    /**
     * If the model wants to translate the for example so names like field_name
     * are not displayed in the history outlook the model can override this method
     * and covert the field name here.
     *
     * @param $field_name
     *
     * @return mixed
     */
    public function translateName($field_name)
    {

        return $field_name;
    }

    /**
     * If the model wants to translate the data for the history update it can override this
     * method and perform the action here. For example, if the model wants to save the value of 'yes'
     * instead of 1 which is much easier to understand when viewing history.
     *
     * @param $field_name
     * @param $field_value
     *
     * @return mixed
     */
    public function translateValue($field_name, $field_value)
    {

        return $field_value;
    }

}

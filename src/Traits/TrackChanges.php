<?php

namespace SGT\Traits;

use SGT\Model\Change;
use SGT\Observer\ChangeObserver;

trait TrackChanges
{

    public static function bootTrackChanges()
    {

        if (isset(self::$change_observer))
        {
            self::observe(self::$change_observer);
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

    /**
     * If the model wants to translate the for example so names like field_name
     * are not displayed in the history outlook the model can override this method
     * and covert the field name here.
     *
     * @param $field_name
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
     * @return mixed
     */
    public function translateValue($field_name, $field_value)
    {

        return $field_value;
    }

    public function changes()
    {

        return $this->morphMany(Change::class, 'reportable');
    }

}
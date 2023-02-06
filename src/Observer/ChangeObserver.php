<?php

namespace SGT\Observer;

class ChangeObserver
{

    public function created($model)
    {

        $this->recordFieldChange($model);
    }

    public function creating($model)
    {

    }

    public function deleted($model)
    {

        $model->addChangeNotice('deleted');
    }

    public function deleting($model)
    {

    }

    public function saving($model)
    {
    }

    public function updated($model)
    {

        $this->recordFieldChange($model);
    }

    public function updating($model)
    {
    }

    protected function recordFieldChange($model)
    {

        $fillable = $model->getFillable();
        $changes  = [];

        foreach ($fillable as $field_name)
        {

            $original_value = $model->getOriginal($field_name);

            $field_value = $model->$field_name;

            if ($original_value != $field_value)
            {
                if ($model->ignoreField($field_name) == true)
                {
                    continue;
                }
                $changes[$field_name] = $field_value;
            }
        }

        $model->addChangeNotices($changes);

    }

}

<?php

namespace RobertoPorceddu\ResourceTranslator\Traits\Resources;

trait HasTranslations
{
    public $changeTranslation = null;

    public function updatedChangeTranslation($value)
    {
        $this->redirect(route('translation-manager.switch', [ 'code' => $value ]));
    }
}

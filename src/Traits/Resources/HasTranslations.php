<?php

namespace RobertoPorceddu\ResourceTranslator\Traits\Resources;

trait HasTranslations
{
    public $changeTranslation = null;

    public function updatedChangeTranslation($value)
    {
        $this->redirect(route('translation-manager.switch', [ 'code' => $value ]));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Include translation_group_uuid from query parameter in form data
        // request()->get() checks both input() and query() automatically
        if ($translationGroupUuid = request()->get('translation_group_uuid')) {
            $data['translation_group_uuid'] = $translationGroupUuid;
        }

        return $data;
    }
}

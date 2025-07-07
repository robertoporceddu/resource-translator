<?php

namespace RobertoPorceddu\ResourceTranslator\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TranslationGroupMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $action = Str::afterLast($request->route()->action['as'], '.');

        if($action === 'edit') {
            $currentLocale = session()->get('language') ?? app()->getLocale();
            $resource = ($request->route()->action['controller'])::getResource();
            $recordId = $request->route()->parameter('record');
            $record = $resource::getModel()::withoutGlobalScopes()->find($recordId);
            $currentLanguageRecord = $record->translations()->where('locale', $currentLocale)->first();

            // store translation group uuid in session until hit index
            session()->put('translation_group_uuid', $record->translation_group_uuid);

            // if current language is not the same as the record language
            if($currentLocale !== $record->locale) {
                // if content does not exist for the current language, redirect to create
                if(! $currentLanguageRecord) {
                    return redirect()->to($resource::getUrl('create'));
                }

                // if content exists for the current language, redirect to it
                return redirect()->to($resource::getUrl('edit', ['record' => $currentLanguageRecord->id]));
            }
        }

        // if action is index, forget translation group uuid
        if($action === 'index') {
            session()->forget('translation_group_uuid');
        }

        return $next($request);
    }
}

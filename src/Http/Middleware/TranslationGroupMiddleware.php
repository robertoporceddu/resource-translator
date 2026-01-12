<?php

namespace RobertoPorceddu\ResourceTranslator\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        $route = $request->route();

        // Pass through if no route or action information
        if (! $route || ! isset($route->action['as'])) {
            return $next($request);
        }

        $action = Str::afterLast($route->action['as'], '.');

        if ($action === 'edit') {
            // Pass through if no controller is set
            if (! isset($route->action['controller'])) {
                return $next($request);
            }

            $currentLocale = session()->get('language') ?? app()->getLocale();
            $controller = $route->action['controller'];

            // Pass through if controller doesn't have getResource method
            if (! is_string($controller) || ! method_exists($controller, 'getResource')) {
                return $next($request);
            }

            $resource = $controller::getResource();
            $model = $resource::getModel();

            // continue if model not have translations
            if (! method_exists($model, 'translations')) {
                return $next($request);
            }

            $recordId = $route->parameter('record');
            $record = $model::withoutGlobalScopes()->find($recordId);

            // Pass through if record not found
            if (! $record) {
                return $next($request);
            }

            $currentLanguageRecord = $record->translations()->where('locale', $currentLocale)->first();

            // if current language is not the same as the record language
            if ($currentLocale !== $record->locale) {
                // if content does not exist for the current language, redirect to create
                if (! $currentLanguageRecord) {
                    return $this->redirectToCreate($resource, $record->translation_group_uuid);
                }

                // if content exists for the current language, redirect to it
                return redirect()->to($resource::getUrl('edit', ['record' => $currentLanguageRecord->id]));
            }
        }

        return $next($request);
    }

    /**
     * Redirect to the create page with the translation group UUID.
     */
    protected function redirectToCreate(string $resource, string $translationGroupUuid): RedirectResponse
    {
        return redirect()->to(
            $resource::getUrl('create') . '?' . http_build_query(['translation_group_uuid' => $translationGroupUuid])
        );
    }
}

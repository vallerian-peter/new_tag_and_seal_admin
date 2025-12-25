<?php

namespace App\Filament\GlobalSearch;

use Filament\Facades\Filament;
use Filament\GlobalSearch\Providers\Contracts\GlobalSearchProvider;
use Filament\GlobalSearch\GlobalSearchResult;
use Filament\GlobalSearch\GlobalSearchResults;
use Filament\Resources\Resource;

class ResourceGlobalSearchProvider implements GlobalSearchProvider
{
    public function getResults(string $query): ?GlobalSearchResults
    {
        if (blank($query)) {
            return null;
        }

        $panel = Filament::getCurrentPanel();
        $resources = $panel->getResources();
        
        $results = [];
        
        foreach ($resources as $resourceClass) {
            if (!is_subclass_of($resourceClass, Resource::class)) {
                continue;
            }
            
            // Skip resources that are hidden from navigation
            if ($resourceClass::shouldRegisterNavigation() === false) {
                continue;
            }
            
            $resourceLabel = $resourceClass::getNavigationLabel() ?? $resourceClass::getPluralModelLabel() ?? $resourceClass::getModelLabel();
            $navigationGroup = $resourceClass::getNavigationGroup();
            $navigationGroupLabel = is_string($navigationGroup) ? $navigationGroup : ($navigationGroup?->getLabel() ?? null);
            
            // Search in resource label and navigation group
            $searchText = strtolower($resourceLabel . ' ' . ($navigationGroupLabel ?? ''));
            $queryLower = strtolower($query);
            
            if (str_contains($searchText, $queryLower)) {
                try {
                    $indexUrl = $resourceClass::getUrl('index');
                    
                    $results[] = [
                        'result' => new GlobalSearchResult(
                            title: $resourceLabel,
                            url: $indexUrl,
                            details: $navigationGroupLabel ? ['Group' => $navigationGroupLabel] : [],
                        ),
                        'group' => $navigationGroupLabel ?? 'Other',
                    ];
                } catch (\Exception $e) {
                    // Skip resources that don't have an index page
                    continue;
                }
            }
        }
        
        if (empty($results)) {
            return GlobalSearchResults::make();
        }
        
        // Group results by navigation group for better organization
        $groupedResults = [];
        foreach ($results as $item) {
            $group = $item['group'];
            if (!isset($groupedResults[$group])) {
                $groupedResults[$group] = [];
            }
            $groupedResults[$group][] = $item['result'];
        }
        
        $builder = GlobalSearchResults::make();
        foreach ($groupedResults as $group => $groupResults) {
            $builder->category($group, $groupResults);
        }
        
        return $builder;
    }
}


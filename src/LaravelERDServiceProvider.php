<?php

namespace Worksofallen\LaravelERD;

use Worksofallen\LaravelERD\Commands\LaravelERDCommand;
use Worksofallen\LaravelERD\Diagram\Ribbon;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelERDServiceProvider extends PackageServiceProvider
{
    private static ?Closure $ribbonExtractor = null;

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-erd')
            ->hasConfigFile('laravel-erd')
            ->hasViews()
            ->hasCommand(LaravelERDCommand::class);
    }

    /**
     * @param  Closure(Model):(null|Ribbon)  $extractorClosure
     */
    public static function setRibbonClosure(Closure $extractorClosure): void
    {
        self::$ribbonExtractor = $extractorClosure;
    }

    /**
     * @return Closure(Model):(null|Ribbon) $extractorClosure
     */
    public static function getRibbonClosure(): Closure
    {
        if (! self::$ribbonExtractor) {
            return fn (Model $model) => null;
        }

        return self::$ribbonExtractor;
    }
}

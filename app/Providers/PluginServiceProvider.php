<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    
        if($this->app->isInstalled()){
            $this->app->plugins = \App\Model\Plugin::where('active','1')->get()->keyBy('root_dir');

            $this->registerPluginAutoloaders();

            $this->registerPluginProviders();
            $this->registerPluginEvents();
            $this->registerPluginLanguage();


        }
        
    }


    private function registerPluginAutoLoaders(){
        

       foreach($this->app->plugins as $plugin){

            $autoloader = $plugin->getPath()."vendor/autoload.php";
            if(file_exists($autoloader)){
                require_once($autoloader);
            }
       }


    }


    private function registerPluginProviders(){

            foreach($this->app->plugins as $plugin){

                foreach($plugin->getRegister('addProviders',[]) as $provider){
                    $this->app->register($provider);
                }

            }

    }



    public function registerPluginEvents(){

             foreach($this->app->plugins as $plugin){

                foreach($plugin->getRegister('eventHooks',[]) as $key => $value){
                    foreach($value as $do){
                        \Event::listen($key,$do);
                    }
                }


             }

    }



    private function registerPluginLanguage(){

           if(\Request::is(\Config::get('horizontcms.backend_prefix')."/plugin/run/*")){
      
                $plugin = $this->app->plugins->get(studly_case(\Request::segment(4)));

                $this->loadTranslationsFrom(base_path($plugin->getPath()."/resources/lang"), 'plugin');
                
            }else if(!\Request::is(\Config::get('horizontcms.backend_prefix')."/*")){
                $this->loadTranslationsFrom(base_path("/themes/".\App\Model\Settings::get('theme')."/lang"), 'website');
            }

    }




    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {


    }




}

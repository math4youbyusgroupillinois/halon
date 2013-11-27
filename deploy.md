Deploy
======

1. <pre>
    zip -r /Volumes/source/PUBLIC/mar_previous_deployments/mar_`ruby -e 'puts Time.now.utc.strftime(\"%Y%m%d%H%M%S\")'`.zip \
    /Volumes/source/PUBLIC/mar
  </pre> 
2. `mkdir -p ~/halon_backup && rm ~/halon_backup/*`
3. `cp /Volumes/source/PUBLIC/mar/app/config/app.php ~/halon_backup`
4. `cp /Volumes/source/PUBLIC/mar/app/database/produciton.sqlite ~/halon_backup`
5. `cd /Volumes/source/PUBLIC/mar`
6. `git pull`
7. `git checkout <version>`
8. `git clean -f -d`
9. `composer install -d workbench/northwestern/printer`
10. `composer install`
11. `php artisan migrate`

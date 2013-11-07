Deploy
======

1. zip -r /Volumes/source/PUBLIC/mar_previous/mar_`timestamp`.zip /Volumes/source/PUBLIC/mar
2. <pre>
    rsync -zvr --exclude 'app/storage'  \
      --exclude 'app/database/*.sqlite' \
      --exclude 'app/config/app.php'    \
      --exclude '.git'                  \
      . /Volumes/source/PUBLIC/mar
  </pre>
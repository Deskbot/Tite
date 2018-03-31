dir=/var/www/html

chmod -R u=rwX $dir
chmod -R go=rX $dir
chmod 755 $dir
chmod 700 $dir/db $dir/.git* $dir/permissions.sh
chmod 600 $dir/notes.md
PRIVATE SONG QR — ONE QR FOR ALL SONGS

This ZIP is an OVERLAY for the existing project. It intentionally does NOT include config.php so your working InfinityFree database settings are not overwritten.

FINAL FLOW:
ONE QR CODE -> CUSTOMER PASSWORD -> ALL ACTIVE SONGS -> PLAY / DOWNLOAD

FILE MANAGER STEPS:
1. Keep your existing project and config.php.
2. Upload this ZIP into htdocs (the active copy, directly under htdocs).
3. Extract the ZIP in htdocs. Allow overwrite of the included files.
4. Make sure uploads/songs and uploads/covers still exist.
5. Log in to Admin.
6. Open: https://YOUR-DOMAIN/upgrade_all_songs.php
7. Wait for “Database upgrade completed successfully.”
8. Open Admin -> ONE QR CODE and download the QR.
9. Admin -> + Customer Password. Create one password for each customer; do not select a song.
10. Give the same ONE QR code to every customer. Give each customer their own password.
11. Customer scans QR -> enters password -> sees ALL active songs -> plays/downloads.
12. After the upgrade succeeds, DELETE upgrade_all_songs.php from File Manager.

IMPORTANT:
- Do not replace config.php.
- Do not run the old database.sql on InfinityFree.
- Existing songs, admins, and customer passwords are intended to remain.
- The upgrade removes only the old song_id relationship from customer_access.

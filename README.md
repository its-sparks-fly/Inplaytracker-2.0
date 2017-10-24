# RPG-Inplaytracker<br />
Mit diesem Plugin habt ihr und eure User die Möglichkeit, euer Inplay besser im Auge zu behalten. Es kommt mit ein paar Funktionen, die ich euch hier stichpunktartig näher vorstellen möchte:

<ul>
<li> Taggen seiner Mitspieler
<li> Neues Feld "Inplay-Datum" zur Sortierung von Szenen
<li> Neues Feld "Spielort" zur besseren Orientierung in Szenen
<li> Neues Feld "Tageszeit" zur besseren zeitlichen Verortung von Szenen
<li> Auflistung & Verlinkung der Mitspieler in der Themenuebersicht (forumdisplay.php)
<li> MyAlert-Integration bei neu erstellter Szenen
<li> MyAlert-Integration bei neuer Antwort auf Inplayszene
<li> Übersicht aller aktiver Szenen mit ausstehenden Posts (aller Charaktere über E-Mail-Adresse verknüpft)
<li> Festlegen einer Posting-Reihenfolge (wird bei Übersicht offener Posts beachtet)
<li> Einstellen von "öffentlichen" Szenen oder Szenen ohne feste Reihenfolge
<li> Anzeige der Gesamtanzahl (offener) Szenen im Header
<li> Szenentracker im Profil mit Zählung von Posts / sortiert nach Datum (auch Archiv)
<li> Angabe einer eigenen Zeitrechnung für Foren fernab vom gregorianischen Kalender
<li> Insgesamt-Übersicht über alle aktiven Inplay-Szene (mit Filter-Suche)
</ul>

<b>Admin-Funktionen</b>
<ul>
<li> Finden von inaktiven Szenen seit x Tagen
<li> Finden von Szenen mit gelöschten Charakteren
<li> Update des Plugins über das Admin Cp
</ul>

<h1>Plugin funktionsfähig machen</h1>
<ul>
<li>Die Plugin-Datei ladet ihr in den angegebenen Ordner <b>inc/plugins</b> hoch.
<li>Die Language-Dateien ladet ihr in den entsprechenden Sprachordner.
<li>Das Admin-Modul packt ihr in den Order <b>admin/modules/tools</b>
<li>Das Plugin muss nun im Admin CP unter <b>Konfiguration - Plugins</b> installiert und aktiviert werden
<li>In den Foreneinstellungen findet ihr nun - ganz unten - Einstellungen zu "Inplaytracker". Macht dort eure gewünschten Einstellungen.
</ul><br />

Das Plugin ist nun einsatzbereit. Solltet ihr schon einiges an eurem Forum gemacht haben, und nicht wie ich im Testdurchlauf ein Default-Theme verwenden, kann es sein, dass nicht alle Variablen eingefügt werden. Sollte euch eine Anzeige fehlen, könnt ihr auf folgende Variablen zurückgreifen:

<blockquote>{$menu_inplaytracker}  // Link zur Übersicht der Szenen (header)
* ruft header_inplaytracker auf

{$inplaytracker} // Szenentracker im Profil (member_profile)
* ruft member_profile_inplaytracker auf

{$tracker_partners} // Eingabefeld für Postingpartner (newthread, editpost)
* ruft newthread_inplaytracker_partners auf

{$tracker_date} // Eingabefeld für Inplay-Datum (newthread, editpost)
* ruft newthread_inplaytracker_date auf

{$tracker_ort} // Eingabefeld für Spielort (newthread, editpost)
* ruft newthread_inplaytracker_location auf

{$tracker_daytime} // Eingabefeld für Tageszeit (newthread, editpost)
* ruft newthread_inplaytracker_daytime auf

{$inplaytracker_lastpost} // Zeitpunkt letzter Inplaypost (member_profile)
* ruft member_profile_inplaytracker_lastpost auf</blockquote>

<h1>Template-Änderungen</h1>
Folgende Templates werden durch dieses Plugin <i>neu hinzugefügt</i>:

header_inplaytracker
  
member_profile_inplaytracker
member_profile_inplaytracker_bit
member_profile_inplaytracker_lastpost
  
misc_inplaytracker 
misc_inplaytracker_bit
misc_inplaytracker_overview
misc_inplaytracker_user

newthread_inplaytracker_date 
newthread_inplaytracker_daytime 
newthread_inplaytracker_ort 
newthread_inplaytracker_partners

showthread_inplaytracker 
showthread_inplaytracker_daytime 
showthread_inplaytracker_halfopenscene 
showthread_inplaytracker_location 
showthread_inplaytracker_openscene 
showthread_inplaytracker_order

Folgende Templates werden durch dieses Plugin <i>bearbeitet</i>:
<ul>
<li>header
<li>member_profile
<li>newthread
<li>editpost
</ul>

<h1>Demo</h1><br />
<center>

<img src="http://eightletters.de/plugins/screens/inplaytracker/screen1.jpg" /><br />
http://eightletters.de/plugins/screens/inplaytracker/screen1.jpg<br /><br />

<img src="http://eightletters.de/plugins/screens/inplaytracker/screen2.jpg" /><br />
http://eightletters.de/plugins/screens/inplaytracker/screen2.jpg<br /><br />

<img src="http://eightletters.de/plugins/screens/inplaytracker/screen3.jpg" /><br />
http://eightletters.de/plugins/screens/inplaytracker/screen3.jpg<br /><br />

<img src="http://eightletters.de/plugins/screens/inplaytracker/screen4.jpg" /><br />
http://eightletters.de/plugins/screens/inplaytracker/screen4.jpg<br /><br />

<img src="http://eightletters.de/plugins/screens/inplaytracker/screen5.jpg" /><br />
http://eightletters.de/plugins/screens/inplaytracker/screen5.jpg<br /><br />

<img src="http://eightletters.de/plugins/screens/inplaytracker/screen6.jpg" /><br />
http://eightletters.de/plugins/screens/inplaytracker/screen6.jpg<br /><br />

<img src="http://eightletters.de/plugins/screens/inplaytracker/screen7.jpg" /><br />
http://eightletters.de/plugins/screens/inplaytracker/screen7.jpg<br /><br />

<img src="http://eightletters.de/plugins/screens/inplaytracker/screen8.jpg" /><br />
http://eightletters.de/plugins/screens/inplaytracker/screen8.jpg<br /><br />

</center>

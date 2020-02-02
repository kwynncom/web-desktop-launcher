See the warning in README.md.  Beyond that, I don't know what an .md file is, so I only use that for core stuff.  

The motivation of this is that I find myself doing all sorts of things on the command line that I'd like buttons for.  And I don't want to learn Gnome.  So 
why not create a localhost web application that launches desktop applications and otherwise does common tasks at the push of a button.  

I have cracked the core problem.  In other words, it works.  


ASSUMPTIONS / PREREQS

* MongoDB PHP library installed with composer
* adb installed
* Right now a FIFO is hardcoded at /var/kwynn/fifo  . It is world read and writeable.  
* Before anything else will work, you must lauch fifo.php as described below.
* I'm testing with scrcpy  You'll need to change that if you want something else.
* The battery info commands I'm using are for Android 8.1 or so and probably several versions earlier.  
* You'll need to put in the phone's local (internal) IP address if it's not hooked up to USB.  Actually, I'm probably assuming network scrcpy.  I put the 
address in from a database entry as an exercise in trying to reveal nothing about my internal setup.
* I'm sure I'm missing assumptions.  Have fun.

FIFO.PHP

In Ubuntu Linux (18.04 and related), fifo.php must be a process descendant of Gnome.  That is, one option is to set it up permanently with the gnome-session-properties command.  Within that Gnome app, the command can be: php /home/blah/dir/fifo.php

That won't run until you reboot, though.  "Restarting" Gnome doesn't seem to work. I'd imagine this is indeed some way without rebooting, but anyhow.

If you're running Gnome, terminals spawned from Gnome work fine.  So, from the command line:

$ nohup php fifo.php &

You don't need the nohup or the background, but it works.

So far it seems that "kill" through "kill -8" doesn't work.  "kill -9" is undesirable because it won't clear the database.  Therefore, the best way to kill 
fifo.php is 

$ echo kill > /var/kwynn/fifo

Note that to make fifo work, you don't have to send it "scrcpy" at the moment, just anything with a newline other than "kill".

EXPECTED / HOPED-FOR RESULTS

If you run index.php as cli, hopefully scrcpy will eventually show up.  I don't think I bothered with the battery info.  From a web server (localhost), if the phone is not charging you'll get "{"msg":"","lev":54,"charging":0}" and then perhaps 2 seconds later scrcpy will hopefully run.  "lev" is the battery level or percentage.


HISTORY

I'm pretty sure I started this Friday late afternoon / evening, Jan 31, 2020.  I don't think it was the evening before.  I probably "did" 4 - 5 hours Friday
and then I've done 5 - 6 hours Saturday into Sunday, Feb 2.

So you're getting something very, very new that just barely works.  


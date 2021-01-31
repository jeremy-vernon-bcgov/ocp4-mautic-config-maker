Mautic Config File Generators for OCP4
======================================

This is a simple script to generate the yaml files for a Mautic instance in OCP4.

Ensure you have an ImageStream for you Mautic configured
--------------------------------------------------------

You can pull lightly modified no-root Docker image from Artifactory or [through DockerHub](https://dockerhub.com/jeremyvernon/mautid-docker-noroot)

Be warned, it's thicc because I have not optimized it even a little yet.

Edit the PHP file with your desired variables
---------------------------------------------

This design was selected for its legibility, portability and minimal dependencies. I recognize it isn't "optimal" by any measure.

Variable names should be mostly self explanatory.

YAML files should be applied and verified in sequence
-----------------------------------------------------

They should be applied (and verified) in the following order.

1) DB PersistentVolume
2) App PersistentVolume
3) DB Secret (See Note)
3) DB Deployment Config
4) DB Service

Secret File Needs To Be Manually Submitted
------------------------------------------

I haven't configured the template to do the character encoding properly for the secret - so it should be entered manually via the web GUI.

Application PVC Should Attach after Install
---------------------------------------------------

Because the application code self-mutates, you gotta do the following.

1) Deploy application
2) Run the web-installer
3) RSYNC the /var/www/html/media directory
4) Attach the PV to /var/www/html/media
5) RSYNC the /var/www/html/media back 

You should only need to do this once per instance of the app. Further optimization can be performed by isolating what files can mutate vs. which are stateless - but I haven't had time.
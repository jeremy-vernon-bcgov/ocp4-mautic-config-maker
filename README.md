Mautic Config File Generators for OCP4
======================================

This is a simple script to generate the yaml files for a Mautic instance in OCP4.

Your recipe for success.

Verify You Can Pull from Dockerhub or use an ImageStream
--------------------------------------------------------

You can pull lightly modified no-root Docker image [from Artifactory](https://developer.gov.bc.ca/Artifact-Repositories)  or [through DockerHub](https://dockerhub.com/jeremyvernon/mautid-docker-noroot)

Be sure to check what the latest version of the docker image is, it may become synced with the version indicated in the deploymentconfig file.

Be warned, the image is rill chonky (1.25 GB) because I have not yet optimized it even a little .

Edit the PHP file with your desired variables
---------------------------------------------

This design was selected for its legibility, portability and minimal dependencies. I recognize it isn't "optimal" by any measure.

Variable names should be mostly self explanatory.

YAML files should be applied and verified in sequence
-----------------------------------------------------

They should be applied (and verified) in the following order.

- DB PVC
- App Config PVC
- APP Media PVC
- DB Secret (See Note)
- DB Deployment Config
- DB Service
- App Deployment Config
- 

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
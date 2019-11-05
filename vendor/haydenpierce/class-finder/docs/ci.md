Continuous Integration Notes
----------------------------

Docker containers are registered in the registry that contain an environment for various PHP versions. At the very least
there should be two versions, one of the oldest version of PHP we're supporting and one for the newest. In a perfect world,
we'd also have an image for a Windows server using PHP to gain some CI test coverage with Windows style absolute paths.
However, getting PHP running on a Windows container proved to be too complicated for the value. Until such a time where 
we can get one created (contributions welcome), testing on Windows will be done manually.

The Dockerfiles used to create these images are contained in a different repository: 
[ClassFinderTestContainers](https://gitlab.com/hpierce1102/ClassFinderTestContainers).

The CI scripts themselves are stored here in `/ci/*`, with the config in `/gitlab-ci.yml`.


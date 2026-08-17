#!/bin/sh
ls *.mp4 | sed "s/^/file '/;s/$/'/" > list.txt
ffmpeg -f concat -safe 0 -i list.txt -c copy stitched.mp4

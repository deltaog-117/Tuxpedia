---
title: Kernel
author: Tuxpedia contributors
kind: concept
---
The **kernel** is the core program of an operating system. It runs with full access to the hardware and decides which program gets the processor, how memory is shared, and how programs talk to disks, networks and other devices. Every other program asks the kernel for these services through system calls.

**Linux** is strictly the kernel, first released by Linus Torvalds in 1991. What people call "a Linux distribution" is the kernel together with the software around it: a C library, a shell, core utilities, an [[init|init system]], a [[package-manager|package manager]] and usually a desktop environment. Two distributions can therefore run the same kernel and still feel entirely different.

### Kernel versions and distributions

Distributions choose how closely to follow the kernel project. Rolling releases such as [[arch-linux|Arch Linux]] ship new kernel versions soon after they appear, while stable distributions such as [[debian|Debian]] stay on a long-term-support kernel for years and backport fixes into it. Many distributions also let you install an alternative kernel next to the default one.

### Modules

Much of the kernel's hardware support is built as **modules**, pieces of code that are loaded and unloaded while the system runs. This keeps the core small, and lets a driver arrive without rebuilding the whole kernel.

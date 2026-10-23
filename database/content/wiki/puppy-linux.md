---
title: Puppy Linux
author: Tomasz Wren
distribution: puppy
---
### Overview

**Puppy Linux** is not a single distribution but a family of tiny, fast, easy-to-use Linux systems that load entirely into RAM. A typical Puppy image is a few hundred megabytes, boots in seconds and leaves your hard drive alone, which makes it ideal for reviving old computers, rescuing broken systems and carrying a personal desktop on a USB stick.

Puppy is built with a toolkit called **Woof-CE** ("Woof Community Edition"), which assembles a complete system from the packages of another distribution. This is why there are many "puppies", each based on a different source.

### Key Features

#### Runs from RAM

Puppy copies its core files into memory at boot. Once it has started, you can remove the USB stick or CD, and the system stays quick because it is not constantly reading from a slow disk.

#### Save files and save folders

Your settings and files are stored in a **save file** or **save folder** on any writable medium, which is loaded and layered over the read-only base at start-up. Multiple sessions and configurations are easy to keep and to carry.

#### Lightweight tools

The default desktop is minimal, typically the **JWM** window manager, plus a set of small utilities for networking, partitioning, backups and writing disk images. Puppy has long been known for including a surprising amount of software in a small download.

#### Many flavours from many sources

Woof-CE can build puppies from the binary packages of other distributions. Past and present builds have used Ubuntu, Debian and Slackware, among others, and some of the best known are:

- **BookwormPup** and **TrixiePup**, built from Debian 12 and 13 packages.
- **Ubuntu-based puppies** following recent Ubuntu LTS releases.
- **S15Pup**, built from Slackware 15 packages.
- **VoidPup**, built from Void Linux packages.

Reports on the project's recent builds list TrixiePup and an Ubuntu-based "ResolutePup" with mid-2026 dates, but check the project's download page for current versions.

#### Package formats

Puppy has its own simple **PET** package format, handled by the Package Manager, and it can also use the packages of the distribution it is built from.

### Who It Is For

- **People with old or low-spec hardware** who need a responsive system.
- **Technicians** who want a rescue and recovery disk that boots fast.
- **Users who like portable computing**, carrying their desktop between machines.

The "single user as root" design of older puppies, with fewer security layers than mainstream systems, makes it a poor choice for a hardened or multi-user environment.

### History

- **2003**: Barry Kauler releases the first Puppy as a small Linux for older computers.
- **2000s**: Puppy gains a following with new releases, each supported by an active forum community.
- **2013 onward**: Kauler steps back from day-to-day work and the community maintains Woof-CE.
- **2020s**: Puppies are built on recent Debian, Ubuntu and Slackware sources.

### Comparison

| Feature | Puppy Linux | Tiny Core Linux | Debian |
| :--- | :--- | :--- | :--- |
| **Typical size** | A few hundred MB | 11 to 16 MB (core) | Several GB installed |
| **Runs in RAM** | Yes | Yes | No (by default) |
| **Built from** | Packages of other distros | Its own base | Its own base |
| **Aim** | Easy, light, portable desktop | Extreme minimalism | Universal system |

### Getting Started

- **Official website**: [puppylinux-woof-ce.github.io](https://puppylinux-woof-ce.github.io)
- **Woof-CE on GitHub**: [github.com/puppylinux-woof-CE/woof-CE](https://github.com/puppylinux-woof-CE/woof-CE)
- **Community forum**: [forum.puppylinux.com](https://forum.puppylinux.com)

---
title: Tiny Core Linux
author: Quentin Marsh
distribution: tinycore
---
### Overview

**Tiny Core Linux** is an extremely small graphical Linux distribution. The smallest version, **Core**, is around 11 MB and boots to a command line, and **TinyCore**, at about 16 MB, adds a minimal graphical desktop. It runs entirely in RAM and starts almost instantly, even on very old computers or in a tiny virtual machine.

The idea is to give you a minimal, fast base and let you add only what you need through downloadable **extensions**. Everything else is left out.

### Key Features

#### Three ways to start

- **Core** is the bare base, with no graphics.
- **TinyCore** adds the FLWM window manager and a graphical desktop for a total of around 16 MB.
- **CorePlus** is a larger installer image with extra window managers, wireless support and an installer, for people who want something closer to a regular desktop.

#### Runs in memory

At boot, Tiny Core loads into RAM and does not depend on the disk after that. This makes it quick and also very resistant to damage, because the running system is recreated from the same image on every start.

#### Extensions

Software comes as **extensions** with a `.tcz` suffix. The graphical **Apps** tool or the `tce-load` command downloads and installs them. They are mounted as read-only compressed images, which keeps memory use low. Extensions can be loaded on every boot, on demand or from a stored directory.

#### Backup and persistence

By default, nothing is saved. You can keep your files and settings on a drive and restore them at boot with the `filetool` backup tools, or use persistent home and `opt` folders. This means a Tiny Core system stays fresh and predictable.

#### A community of tinkerers

The project has a loyal forum and many third-party extensions. The Raspberry Pi variant, **piCore**, brings the same approach to ARM boards.

### Releases

Version **17.0** came out in February 2026, and **17.1** is listed with a July 2026 date. Check the project's site for current versions and for the exact sizes, which change slightly between releases.

### Who It Is For

- **Owners of very old or low-memory hardware.**
- **Developers building appliances, kiosks and embedded devices** that must boot fast and cleanly.
- **Learners** who want to see how a small Linux system is put together.

It is not a good fit for people who want a polished, ready-made desktop with all the usual applications.

### History

- **2008**: Robert Shingledecker, a developer from the Damn Small Linux community, releases Tiny Core Linux.
- **2010s**: The project matures with new releases, 64-bit builds and the piCore variant for Raspberry Pi.
- **2026**: Version 17 modernises the kernel and toolchain.

### Comparison

| Feature | Tiny Core Linux | Puppy Linux | Alpine Linux |
| :--- | :--- | :--- | :--- |
| **Base size** | 11 to 16 MB | A few hundred MB | Few MB (containers) |
| **Runs in RAM** | Yes | Yes | Optional diskless mode |
| **Packages** | TCZ extensions | PET and packages of source distro | apk |
| **Aim** | Maximum minimalism | Easy, small desktop | Secure, lean base |

### Getting Started

- **Official website**: [tinycorelinux.net](http://www.tinycorelinux.net)
- **Downloads**: [tinycorelinux.net/downloads.html](http://www.tinycorelinux.net/downloads.html)
- **Wiki**: [wiki.tinycorelinux.net](http://wiki.tinycorelinux.net)
- **Forum**: [forum.tinycorelinux.net](http://forum.tinycorelinux.net)

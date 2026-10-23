---
title: Gentoo Linux
author: Quentin Marsh
distribution: gentoo
---
### Overview

**Gentoo Linux** is a source-based, rolling-release distribution built around a powerful package manager called **Portage**. Instead of downloading ready-made binaries, a typical Gentoo user compiles software on their own machine with options chosen to fit their needs. The result is a system that is tuned to the user's hardware and wishes, at the price of time and learning.

Gentoo describes itself as a **meta-distribution**: you do not install a finished product, you build exactly the one you want from a set of sources. The project is named after the fast-swimming gentoo penguin.

### Key Features

#### Portage and ebuilds

Software is described by **ebuilds**, small scripts that tell Portage how to fetch, build and install a package. The `emerge` command resolves dependencies and builds everything in the right order. A 2025 project retrospective reported around 19,000 packages in the main repository.

#### USE flags

Gentoo's signature idea is the **USE flag**: a global or per-package keyword such as `wayland`, `-systemd` or `vulkan` that switches optional features on or off at build time. Turn off what you do not need and the dependencies disappear from your system with it.

#### Init system choice

Gentoo lets you pick your init system. **OpenRC** is the traditional choice, and **systemd** is fully supported; stage3 tarballs are published for both.

#### Binary packages are available too

Compiling everything is no longer mandatory. The project offers **binary package hosts**, and you can mix them with source builds, which cuts installation time for common packages while keeping customisation where it counts.

#### The Handbook

Installation is manual, guided by the extremely thorough **Gentoo Handbook**. You partition the disk, unpack a **stage3** archive (a minimal ready-to-use root filesystem), configure the kernel and install the bootloader. Many users say the process taught them more about Linux than any other.

### Who It Is For

- **Tinkerers and learners** who want to understand the build and boot process.
- **Power users** who want fine control over features, compiler flags and the kernel.
- **Embedded and special-purpose builders**, because Gentoo's tools can also create custom systems.

It is a poor choice if you want an install that takes twenty minutes or a computer that never needs attention.

### History

- **1999**: Daniel Robbins starts **Enoch Linux**, an early experiment in a source-based distribution.
- **2002**: Gentoo Linux 1.0 is released.
- **2004**: The **Gentoo Foundation** is established as a non-profit to hold the project's assets.
- **Today**: Development is carried by volunteer developers, with the Foundation and the Gentoo e.V. in Germany supporting it.

### Comparison

| Feature | Gentoo | Arch Linux | Debian |
| :--- | :--- | :--- | :--- |
| **Package style** | Source first, binaries optional | Binary | Binary |
| **Build-time options** | USE flags per package | Mostly upstream defaults | Debian-chosen defaults |
| **Init system** | OpenRC or systemd | systemd | systemd |
| **Install effort** | High, manual | Medium to high | Low |

### Getting Started

- **Official website**: [gentoo.org](https://www.gentoo.org)
- **Handbook**: [wiki.gentoo.org/wiki/Handbook:Main_Page](https://wiki.gentoo.org/wiki/Handbook:Main_Page)
- **Gentoo Wiki**: [wiki.gentoo.org](https://wiki.gentoo.org)
- **Downloads and stage3 files**: [gentoo.org/downloads](https://www.gentoo.org/downloads/)
- **Project retrospective (LWN)**: [lwn.net/Articles/1053289](https://lwn.net/Articles/1053289/)

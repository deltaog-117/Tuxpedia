---
title: Slackware
author: Tomasz Wren
distribution: slackware
---
### Overview

**Slackware** is the oldest Linux distribution that is still maintained, and it has been led from the start by one person, **Patrick Volkerding**. It follows the **KISS** principle in a literal way: the system is plain, close to upstream and designed to be understood by reading the files that make it up.

Slackware does not try to hide how Unix works. There are no graphical configuration wizards for everything, no automatic dependency resolution in the base package tools and no systemd. What you get is a conservative, stable and very predictable system that many users run for decades.

### Key Features

#### Simple package tools

Packages are tarballs with an extension such as `.txz`, handled by `installpkg`, `upgradepkg` and `removepkg`. These tools do **not** resolve dependencies. Slackware instead installs a full set of software by default, so dependencies are normally already present. The community tool `slackpkg` downloads updates, and **SlackBuilds.org** supplies build scripts for thousands of extra applications.

#### BSD-style init

Slackware uses a **BSD-style** init with a handful of readable shell scripts in `/etc/rc.d`. Enabling a service means making its script executable. It is easy to follow and easy to change.

#### Stays close to upstream

Software is lightly patched and configured the way its authors intended. This makes Slackware a good platform for learning how things work and for following upstream documentation.

#### A full install by default

The installer lets you pick software series, and the default is to install nearly everything: desktops, compilers and servers. You get KDE Plasma and other desktops ready to use after a single install.

#### Long lifetimes

Releases come irregularly, often years apart, and each is supported for a long time. Slackware **15.0** was released in February 2022, more than five years after the previous release, 14.2.

### Current Development

The development branch, **Slackware -current**, is where the next release is prepared. In September 2026 Phoronix reported that Patrick Volkerding was calling the latest state of -current **Slackware 16 Alpha 1**, following an update of the toolchain to GCC 16.2, glibc 2.44 and Binutils 2.47. Alpha status means -current is not yet recommended for machines that must stay stable.

### Who It Is For

- **Learners** who want to see how a traditional Linux system fits together.
- **Long-term users** who value stability and a stable way of doing things.
- **Anyone who wants no surprises** from upgrades or hidden configuration layers.

Newcomers may find the lack of dependency handling in the base tools unfamiliar at first.

### History

- **1993**: Patrick Volkerding releases Slackware 1.0 in July, derived from the SLS distribution. It is one of the first widely used Linux distributions.
- **1990s**: Slackware provides the base for early versions of SUSE Linux.
- **2022**: Slackware 15.0 ends a long wait.
- **2026**: Slackware 16 enters alpha testing.

### Comparison

| Feature | Slackware | Debian | Gentoo |
| :--- | :--- | :--- | :--- |
| **First release** | 1993 | 1993 | 2002 |
| **Dependency handling** | Not in the base tools | Automatic (APT) | Automatic (Portage) |
| **Init** | BSD-style scripts | systemd | OpenRC or systemd |
| **Release rhythm** | Irregular | About every two years | Rolling |

### Getting Started

- **Official website**: [slackware.com](http://www.slackware.com)
- **Documentation project**: [docs.slackware.com](https://docs.slackware.com)
- **SlackBuilds.org**: [slackbuilds.org](https://slackbuilds.org)
- **Slackware 16 Alpha coverage**: [Phoronix](https://phoronix.com/news/Slackware-16-Alpha)

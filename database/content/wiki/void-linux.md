---
title: Void Linux
author: Elio Brandt
distribution: void
---
### Overview

**Void Linux** is an independent, rolling-release distribution that is not a fork or derivative of any other. It was written from scratch with its own package manager, **XBPS**, its own build system, **xbps-src**, and the **runit** init system instead of systemd. It is fast, small and unusually transparent, and it has a loyal following among people who like to know what their system is doing.

Void is one of the few distributions that gives you a real choice of C library: separate repositories and installation images exist for **glibc** and for **musl**.

### Key Features

#### XBPS

**XBPS** (the X Binary Package System) is the package manager. It is quick, has a simple dependency solver and stores packages in a straightforward format. Common operations use the `xbps-install`, `xbps-remove` and `xbps-query` commands.

#### xbps-src

Every package is built from a template using **xbps-src**, a clean-room build system that compiles in a minimal chroot or container. Templates are short shell-like files, which makes contributing a package easy. Users can build their own packages or patch existing ones with the same tool.

#### runit

Services are supervised by **runit**, a tiny process supervisor and init. A service is just a directory with a `run` script, and enabling it means linking that directory into the service folder. Boot is fast, and the whole system can be understood by reading a few scripts.

#### glibc or musl

You pick a C library at install time. glibc gives the widest software compatibility, and musl gives a smaller, stricter system that is popular for lean or static builds. Note that proprietary software built for glibc may not run on the musl variant.

#### Rolling, with no preinstalled desktop

Void ships "base" images that boot to a console, and "live" images with a few common desktops such as Xfce, Cinnamon, MATE or LXQt. You then add only what you want. It is rolling, so updates arrive continuously.

### Who It Is For

- **Users who dislike systemd** and want a simple, readable init.
- **Minimalists and tinkerers** who enjoy building their desktop from components.
- **Packagers** who like the idea of contributing templates rather than learning a complex specification.

It is less suited to anyone who wants a desktop that is configured for them from the first boot.

### History

- **2008**: Juan Romero Pardines (known as xtraeme) starts Void Linux, together with the XBPS package manager.
- **2018**: The project's original maintainer goes quiet and the core team moves the website and repositories to new infrastructure, organising the community as a collective of volunteers.
- **Today**: A group of volunteers maintains the distribution and its package repositories.

### Comparison

| Feature | Void Linux | Artix Linux | Alpine Linux |
| :--- | :--- | :--- | :--- |
| **Origin** | Independent | Arch-based | Independent |
| **Init** | runit | OpenRC, runit, s6 or dinit | OpenRC |
| **Package manager** | XBPS | pacman | apk |
| **C library** | glibc or musl | glibc | musl |
| **Release model** | Rolling | Rolling | Fixed, every six months |

### Getting Started

- **Official website**: [voidlinux.org](https://voidlinux.org)
- **Handbook**: [docs.voidlinux.org](https://docs.voidlinux.org)
- **Downloads**: [voidlinux.org/download](https://voidlinux.org/download/)
- **Package templates**: [github.com/void-linux/void-packages](https://github.com/void-linux/void-packages)

---
title: Arch Linux
author: Tomasz Wren
distribution: arch
---
### Overview

**Arch Linux** is an independent, rolling-release distribution built around simplicity as its developers define it: a minimal base, no hidden configuration, and the user in charge. Rather than offering a graphical installer and a finished desktop, Arch gives you a command-line system and expects you to assemble the rest.

Its guiding idea is the **KISS principle** (Keep It Simple, Stupid), applied to the *design* of the system rather than to how easy it is to install. Arch aims to ship software close to how upstream authors released it, with few distribution-specific patches.

### Key Features

#### Rolling release

There are no version numbers to upgrade between. A single `pacman -Syu` brings the whole system to the current state of the repositories, so new kernels, compilers and desktop environments reach you quickly. The trade-off is that you are responsible for reading the news and handling the occasional manual intervention.

#### pacman and the Arch Build System

Packages are managed by **pacman**, a fast, simple tool that handles installation, removal, dependency resolution and cryptographic signature checks. The official repositories are `core`, `extra` and `multilib`. Packages are described by short `PKGBUILD` scripts, and the **Arch Build System (ABS)** and `makepkg` make it easy to build your own.

#### The Arch User Repository (AUR)

The **AUR** is a community collection of PKGBUILDs for software that is not in the official repositories. You download a recipe, review it and build it with `makepkg`. Helpers exist, but they are not part of the official system, and AUR content is **unvetted**, so read what you install.

#### The Arch Wiki

The **Arch Wiki** is widely regarded as one of the best Linux documentation resources anywhere, and is useful even to people who run other distributions. Many tutorials for other systems link to it.

#### Installation

The classic installation is done by hand from a live environment: partition the disk, install the base system with `pacstrap`, configure the bootloader and create users. An official `archinstall` guided installer is also included on the ISO for those who want a shortcut.

### Who It Is For

- **Enthusiasts and learners** who want to understand every part of their system.
- **Developers** who need current toolchains and libraries.
- **Users who want a custom environment** without removing a pile of preinstalled defaults.

It is a poor match for people who want an unattended, install-and-forget machine, or for production servers that must change rarely.

### History

- **2002**: Judd Vinet starts Arch Linux, inspired by the simplicity of CRUX.
- **2007**: Aaron Griffin takes over as project leader.
- **2012**: Arch moves to **systemd** as its init system.
- **2020s**: A second generation of leadership and funding arrangements formalises how the project is run, and the distribution becomes the base of many derivatives, among them Manjaro, EndeavourOS and [[artix|Artix]].

### Comparison

| Feature | Arch Linux | Debian | Gentoo |
| :--- | :--- | :--- | :--- |
| **Release model** | Rolling | Fixed | Rolling |
| **Packages** | Binary (pacman) | Binary (APT) | Mostly source (Portage), binaries optional |
| **Installation** | Manual, with an optional guided installer | Graphical or text installer | Manual, following the Handbook |
| **Philosophy** | Simple design, up to date | Stable and free | Maximum configurability |

### Getting Started

- **Official website**: [archlinux.org](https://archlinux.org)
- **Arch Wiki**: [wiki.archlinux.org](https://wiki.archlinux.org)
- **Installation guide**: [wiki.archlinux.org/title/Installation_guide](https://wiki.archlinux.org/title/Installation_guide)
- **Package search**: [archlinux.org/packages](https://archlinux.org/packages/)
- **AUR**: [aur.archlinux.org](https://aur.archlinux.org)

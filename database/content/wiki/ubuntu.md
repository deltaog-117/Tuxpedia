---
title: Ubuntu
author: Idris Calloway
distribution: ubuntu
---
### Overview

**Ubuntu** is a [[debian|Debian]]-based Linux distribution published by **Canonical Ltd.** and one of the most widely used Linux systems on desktops, servers and in the cloud. Its name comes from a Southern African philosophy often summed up as "I am what I am because of who we all are".

Ubuntu takes Debian's package base, tests and polishes it on a fixed schedule, and adds a friendly installer, sensible defaults and commercial support options. That combination made it the default "first Linux" for many people and a common image on cloud platforms.

### Key Features

#### A predictable release calendar

A new version appears every **six months**, in April and October. Every second April, in even-numbered years, the release is a **Long Term Support (LTS)** version with five years of standard security updates. **Ubuntu Pro**, which is free for personal use on a limited number of machines, extends coverage for LTS releases much further.

Ubuntu 26.04 LTS, codenamed "Resolute Raccoon", was scheduled for 23 April 2026. Reports describe it as shipping Linux 7.0 and GNOME 50, and as the first LTS whose default GNOME session is Wayland-only.

#### Packaging: APT, Snap and Flatpak

Ubuntu uses Debian's `.deb` packages and **APT**. It also pushes **Snap**, Canonical's containerised package format, for some applications; the Firefox package, for example, is delivered as a snap. Flatpak is not installed by default but works fine, and some flavours ship it.

#### Tooling for servers and clouds

- **Netplan** describes network configuration in YAML.
- **cloud-init** configures instances at first boot on almost every cloud provider.
- **LXD, MicroK8s and Multipass** make system containers, local Kubernetes clusters and throwaway virtual machines easy to start.
- Hardware certification programmes list laptops and servers that are tested against Ubuntu.

#### Flavours

Official community flavours reuse the same base with a different desktop: **Kubuntu** (KDE Plasma), **Xubuntu** (Xfce), **Lubuntu** (LXQt), **Ubuntu MATE**, **Ubuntu Budgie**, **Ubuntu Studio** and others.

### Who It Is For

- **Newcomers** who want a system with a polished installer and plenty of tutorials written for it.
- **Developers and teams** who want a long-lived, widely supported platform for workstations and deployments.
- **Cloud and container users**, since Ubuntu images are available on most providers.

Some users dislike Snap's role or Canonical's decisions about defaults; [[debian|Debian]], [[linux-mint|Linux Mint]] and the flavours exist partly as alternatives.

### History

- **2004**: Mark Shuttleworth founds Canonical and releases Ubuntu 4.10 "Warty Warthog".
- **2006**: The first LTS release, 6.06 "Dapper Drake".
- **2011**: The Unity desktop replaces the classic GNOME 2 interface.
- **2017**: Unity is dropped and GNOME returns as the default desktop.
- **2026**: Ubuntu 26.04 LTS follows 24.04 LTS as the current long-term release.

### Comparison

| Feature | Ubuntu | Debian | Linux Mint |
| :--- | :--- | :--- | :--- |
| **Base** | Debian | Independent | Ubuntu (LMDE uses Debian) |
| **Release cadence** | Six months, LTS every two years | About two years | Follows the Ubuntu LTS base |
| **Default desktop** | GNOME | GNOME | Cinnamon |
| **Backed by** | Canonical | Community | Small team and donations |

### Getting Started

- **Official website**: [ubuntu.com](https://ubuntu.com)
- **Download**: [ubuntu.com/download](https://ubuntu.com/download)
- **Documentation**: [documentation.ubuntu.com](https://documentation.ubuntu.com)
- **Community help**: [askubuntu.com](https://askubuntu.com) and [discourse.ubuntu.com](https://discourse.ubuntu.com)
- **Release schedule**: [wiki.ubuntu.com/Releases](https://wiki.ubuntu.com/Releases)

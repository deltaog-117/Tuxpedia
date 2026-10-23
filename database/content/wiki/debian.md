---
title: Debian
author: Mara Voss
distribution: debian
---
### Overview

**Debian** is a community-built, non-commercial Linux distribution and one of the oldest still in active development. It is the ancestor of a huge family tree, including Ubuntu, Linux Mint, Tails and Raspberry Pi OS, so a large share of the Linux world runs on Debian packages without knowing it.

The project is governed by the **Debian Social Contract** and the **Debian Free Software Guidelines (DFSG)**, a written promise to keep the system free software and to give priority to users. Decisions are made by volunteer developers under a published constitution, and the **Debian Project Leader** is elected every year.

### Key Features

#### Three branches, one rule: stable means stable

Debian maintains several branches at once:

- **Stable** receives only security fixes and critical bug fixes, which makes it a favourite for servers.
- **Testing** collects packages that have spent time in unstable without serious bugs and becomes the next stable release.
- **Unstable (sid)** is where new versions land first. It is rolling and sometimes rough.

A new stable release arrives roughly every two years, and each one is named after a character from the *Toy Story* films.

#### APT and dpkg

Software is distributed as `.deb` packages, handled by **dpkg** at the low level and **APT** (`apt`, `apt-get`) for dependency resolution and downloads. The archive is one of the largest in the Linux world, with tens of thousands of packages.

#### Wide architecture support

Debian supports many CPU architectures, including amd64, arm64, armhf, ppc64el, riscv64 and s390x. Debian 13 removed i386 as a regular architecture, although some 32-bit libraries remain available for running old software.

#### Reproducible builds and security

The project is a leader in **reproducible builds**, where the same source is meant to produce bit-for-bit identical packages, so anyone can check that a binary matches its source. A dedicated security team publishes **Debian Security Advisories (DSAs)**, and the **Debian LTS** effort extends support for older releases.

### Who It Is For

- **Server administrators** who want a predictable base that changes slowly.
- **Developers** who want a clean, well-documented system without vendor extras.
- **People who care about software freedom**, since the default archive is free software and non-free firmware is clearly separated.
- **Anyone building their own distribution**, because Debian is the most common starting point for derivatives.

Desktop users are served well too, with GNOME as the default and KDE Plasma, Xfce, MATE, Cinnamon and LXQt available at install time. The trade-off is that stable packages can be a year or two behind upstream.

### History

- **1993**: Ian Murdock announces Debian on 16 August. The name combines his first name with that of his wife, Debra.
- **1996**: Debian 1.1 "Buzz" is the first release carrying a codename.
- **1997**: The Debian Social Contract and the DFSG are adopted.
- **2025**: Debian 13 "Trixie" is released on 9 August.

As of October 2026 Debian 13 is the stable release, Debian 12 "Bookworm" is covered by long-term support, and Debian 14 "Forky" is in testing.

### Comparison

| Feature | Debian | Ubuntu | Arch Linux |
| :--- | :--- | :--- | :--- |
| **Release model** | Fixed, about every two years | Fixed, every six months (LTS every two years) | Rolling |
| **Package format** | .deb (APT) | .deb (APT, plus Snap) | pacman packages |
| **Governance** | Volunteer community | Canonical | Volunteer community |
| **Typical use** | Servers, stable desktops, base for derivatives | Desktops, cloud, beginners | Hands-on users who want current software |

### Getting Started

- **Official website**: [debian.org](https://www.debian.org)
- **Installation guide and documentation**: [debian.org/doc](https://www.debian.org/doc/)
- **Debian Wiki**: [wiki.debian.org](https://wiki.debian.org)
- **Release information**: [debian.org/releases](https://www.debian.org/releases/)
- **Package search**: [packages.debian.org](https://packages.debian.org)

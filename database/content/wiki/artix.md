---
title: Artix
author: Jean Flechett
distribution: artix
---
### Overview

**Artix Linux** (usually just **Artix**) is a rolling-release Linux distribution based on Arch Linux. It shares Arch's philosophy but makes one deliberate break: **it does not use systemd** as its init system.

The name is said to come from "the art of Linux", which reflects its focus on simplicity, modularity and user choice. Artix lets you pick your init system, **OpenRC**, **runit**, **s6** or **dinit**, rather than accepting a single default.

Artix maintains its own repositories and works with the **Arch User Repository (AUR)**, which opens up a very large pool of extra software. Its main platform is x86-64.

### Core Features

#### 1. No systemd, and a Choice of Init

Artix's defining trait is its rejection of systemd. During installation you choose one of four init systems:

- **OpenRC**: a dependency-based traditional init, and historically the most popular choice on Artix.
- **runit**: a small service supervision suite known for simplicity and fast boot.
- **s6**: a complete supervision toolkit that emphasises correctness and security.
- **dinit**: a newer, simple init with parallel startup and service supervision.

Each choice has its own set of service packages in the Artix repositories, so services are installed and enabled in a way that fits your init.

#### 2. Rolling Release

Like Arch, Artix is **rolling**. There are no version-to-version upgrades; the system receives updates continuously, and new installation images are published from time to time so fresh installs start close to current.

#### 3. Arch Heritage: pacman and the AUR

Artix uses the same **pacman** package manager as Arch and can build AUR packages. Packages that integrate with the init system, however, come from the Artix repositories. The project provides an optional package that lets you enable Arch's `extra` and `multilib` repositories, and recommends being careful with anything that depends on systemd.

#### 4. Minimal by Default

Artix installs a small base system and leaves the choices to you. Many users report very fast boot times and a light system overall.

#### 5. Modern Components

In its **2026.04** release, Artix switched its default display server from Xorg to **XLibre** (a fork of Xorg) and its default audio server from PulseAudio to **PipeWire**. The XLibre choice was short-lived: the developers reportedly ended XLibre support after regressions and maintenance burden, and by the 2026.08 release Xorg had returned as the default. If you are installing today, check the current release notes.

#### 6. GNOME Support Ended

In October 2025 Artix announced that it would stop supporting the **GNOME desktop**. GNOME 49 removed the non-systemd fallback in `gnome-session`, and the Artix team decided it did not have the time or interest to maintain its own replacement. The affected packages are `gnome-session`, `gnome-shell`, `mutter` and `gnome-settings-daemon`; standalone GNOME applications remain packaged. KDE Plasma, Cinnamon, Xfce, MATE, LXDE and LXQt are still offered.

### Use Cases

Artix suits:

- **Users who prefer a traditional init** and find systemd too large or complex.
- **Experienced Arch users** who want its package model without systemd.
- **Developers and administrators** who need to work or test with OpenRC, runit, s6 or dinit.
- **People with older or lower-spec hardware**, since the base is light.

### History

- **2012**: The **Arch OpenRC** project starts, offering OpenRC as an alternative to systemd on Arch. A similar effort, **Manjaro OpenRC**, appears at about the same time.
- **2017**: The two projects merge to create Artix Linux.
- **Early releases**: Artix offers a minimal command-line image and a graphical Calamares-based image with LXQt, later adding more desktops.
- **Today**: Images for several desktops are available, each in four init variants (OpenRC, runit, s6 and dinit). Community editions with OpenRC only are aimed at users who want less tinkering.

### Comparison with Other Distributions

| Feature | Artix Linux | Arch Linux | Void Linux | NixOS |
| :--- | :--- | :--- | :--- | :--- |
| **Base** | Arch Linux | Independent | Independent | Independent |
| **Release model** | **Rolling** | Rolling | Rolling | Fixed, twice a year |
| **Init system** | **Your choice**: OpenRC, runit, s6, dinit | systemd | runit | systemd |
| **Package manager** | **pacman** | pacman | XBPS | Nix |
| **Repositories** | Artix repos plus AUR | Official repos plus AUR | Official repos | Nixpkgs |
| **Philosophy** | Traditional layout, no systemd | Traditional layout, KISS | Independent, simple tools | Declarative, isolated |

> One reviewer put it this way: "If you like doing things your own way, and you enjoy an OS that challenges your Linux skills just enough to be fun, Artix might be perfect for you."

### Getting Started

- **Official website**: [artixlinux.org](https://artixlinux.org)
- **Wiki**: [wiki.artixlinux.org](https://wiki.artixlinux.org) holds community-maintained documentation and guides
- **Forum**: [forum.artixlinux.org](https://forum.artixlinux.org)
- **GNOME announcement coverage**: [Linuxiac](https://linuxiac.com/artix-linux-drops-gnome-desktop-support-over-systemd-dependence/)
- **2026.04 release coverage**: [Linuxiac](https://linuxiac.com/artix-linux-2026-04-released-with-xlibre-as-default-x-serve/)
- **XLibre reversal**: [Linuxiac](https://linuxiac.com/artix-linux-drops-xlibre-support-just-months-after-making-it-default/)

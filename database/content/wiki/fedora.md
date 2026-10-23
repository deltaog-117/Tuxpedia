---
title: Fedora
author: Noor Halvorsen
distribution: fedora
---
### Overview

**Fedora Linux** is a community distribution sponsored by **Red Hat**. It aims to deliver recent, upstream-faithful software on a polished desktop and serves as the proving ground for technology that later reaches Red Hat Enterprise Linux (RHEL). New things tend to appear in Fedora first.

The project's four foundations are **Freedom, Friends, Features and First**: free software, a welcoming community, useful functionality and a bias towards innovation.

### Key Features

#### Leading edge, but not rolling

Fedora publishes a new release roughly every **six months**, and each one is supported for about **13 months**. That keeps the software current without the constant change of a rolling release. Fedora 44 arrived in April 2026 with GNOME 50 and a Linux 6.19 kernel, and Fedora 45 was scheduled for late October 2026.

#### Editions and spins

- **Workstation**: GNOME desktop for laptops and desktops.
- **Server**: a base for servers and virtual machines.
- **IoT** and **CoreOS**: images for edge devices and container hosts.
- **Spins and labs**: KDE Plasma, Xfce, Cinnamon, MATE, LXQt, Sway, i3 and special-purpose collections.
- **Atomic desktops**: **Silverblue** (GNOME), **Kinoite** (KDE Plasma) and others use an image-based, rollback-friendly design with applications from Flatpak.

#### Modern defaults

Fedora is often the first major distribution to adopt new building blocks: **Wayland**, **PipeWire**, **systemd-oomd**, the **Btrfs** filesystem as default (since Fedora 33), and **SELinux** enforcing by default for mandatory access control.

#### DNF and RPM

Software is packaged as `.rpm` files and managed with **DNF**. The **RPM Fusion** third-party repositories supply codecs and drivers that Fedora cannot ship for legal reasons. Flatpak applications from Flathub are fully supported.

### Who It Is For

- **Developers** who want current compilers, languages and containers tooling on a stable-enough base.
- **People targeting RHEL** or its rebuilds, since Fedora previews what is coming.
- **Desktop users** who like GNOME or KDE Plasma as the upstream authors designed them.

Because releases are supported for a limited time, you need to upgrade about once a year. The `dnf system-upgrade` workflow makes that straightforward.

### History

- **2003**: Red Hat ends the boxed Red Hat Linux product; the community-driven Fedora Project is created.
- **2003**: Fedora Core 1 is released in November.
- **2011**: Fedora 15 brings GNOME 3 to the main release.
- **2020**: Fedora 33 makes Btrfs the default filesystem.
- **2026**: Fedora 44 is released.

### Comparison

| Feature | Fedora | Ubuntu | openSUSE Tumbleweed |
| :--- | :--- | :--- | :--- |
| **Cadence** | About every six months | Six months, LTS every two years | Rolling |
| **Package format** | RPM (DNF) | .deb (APT) | RPM (zypper) |
| **Support window** | About 13 months | 9 months, or 5 years for LTS | Latest snapshot only |
| **Default security** | SELinux | AppArmor | AppArmor or SELinux |

### Getting Started

- **Official website**: [fedoraproject.org](https://fedoraproject.org)
- **Documentation**: [docs.fedoraproject.org](https://docs.fedoraproject.org)
- **Fedora Magazine**: [fedoramagazine.org](https://fedoramagazine.org)
- **RPM Fusion**: [rpmfusion.org](https://rpmfusion.org)
- **Atomic desktops**: [fedoraproject.org/atomic-desktops](https://fedoraproject.org/atomic-desktops/)

---
title: openSUSE
author: Elio Brandt
distribution: opensuse
---
### Overview

**openSUSE** is a community-driven Linux project sponsored by **SUSE**, with a strong reputation for engineering quality and for its tooling. It is unusual in offering two very different flavours under one name: a conservative fixed release for people who want stability, and a rolling release for people who want the newest software.

The project builds its software with the **Open Build Service (OBS)**, a public platform that can compile packages for many distributions, and tests every Tumbleweed snapshot automatically with **openQA**.

### The Main Editions

#### Leap

**Leap** is the fixed-release edition. Leap 16.0, released on 1 October 2025, is built on the same code base as **SUSE Linux Enterprise Server 16**, so it inherits enterprise-grade packages and a long maintenance window. It is installed with **Agama**, a new web-based installer that can also be driven remotely. Leap 16 requires a CPU that supports the x86-64-v2 level.

#### Tumbleweed

**Tumbleweed** is a **rolling release** that ships fresh upstream software in tested snapshots. Every snapshot passes through openQA before it reaches users, which makes Tumbleweed more dependable than rolling releases usually are.

#### Slowroll

**Slowroll** sits between the two. It is a rolling release that takes Tumbleweed's updates in larger, slower batches (roughly monthly), giving fewer interruptions without a hard version jump.

#### MicroOS, Aeon and Kalpa

**openSUSE MicroOS** is an immutable, transactional system for container hosts. **Aeon** (GNOME) and **Kalpa** (KDE Plasma) are desktop variants built on the same idea.

### Key Features

- **Btrfs with snapshots by default.** The installer proposes Btrfs and **Snapper** takes a snapshot before and after every package transaction. If an update goes wrong you can boot an earlier snapshot from the GRUB menu.
- **zypper and RPM.** `zypper` is a fast, solver-based package manager, and packages use the RPM format.
- **YaST and its successors.** Administrators have long relied on **YaST**, the all-in-one configuration tool. Leap 16 moves installation to Agama, while some YaST modules continue in other forms.
- **Desktop choice.** KDE Plasma and GNOME are both first-class, with Xfce, Cinnamon, MATE and others available.

### Who It Is For

- **Developers and system administrators** who value Btrfs snapshots, strong tooling and a clear path between fixed and rolling releases.
- **Users who want KDE Plasma** from a well-integrated distribution.
- **Teams already using SUSE products**, who can develop on openSUSE and deploy on SUSE Linux Enterprise.

### History

- **1992**: SUSE is founded in Germany; its Linux distribution begins as a German adaptation of [[slackware|Slackware]].
- **2005**: SUSE Linux is opened to the community as the **openSUSE Project** after Novell's acquisition.
- **2015**: Leap is introduced, built on SUSE Linux Enterprise sources.
- **2025**: Leap 16.0 and the Agama installer arrive.

### Comparison

| Feature | openSUSE Leap | openSUSE Tumbleweed | Fedora |
| :--- | :--- | :--- | :--- |
| **Model** | Fixed | Rolling | Fixed, about every six months |
| **Based on** | SUSE Linux Enterprise | Independent | Independent (sponsored by Red Hat) |
| **Rollback** | Btrfs snapshots | Btrfs snapshots | Atomic editions only |
| **Package format** | RPM (zypper) | RPM (zypper) | RPM (DNF) |

### Getting Started

- **Official website**: [opensuse.org](https://www.opensuse.org)
- **Downloads**: [get.opensuse.org](https://get.opensuse.org)
- **Documentation**: [doc.opensuse.org](https://doc.opensuse.org)
- **Open Build Service**: [build.opensuse.org](https://build.opensuse.org)
- **Release announcement for Leap 16.0**: [linuxiac.com](https://linuxiac.com/opensuse-leap-16-0-released-this-is-whats-new/)

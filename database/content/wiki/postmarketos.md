---
title: postmarketOS
author: Idris Calloway
distribution: postmarketos
---
### Overview

**postmarketOS** (often written pmOS) is a Linux distribution for phones, tablets and other devices that normally run Android or iOS. It is built on **Alpine Linux** and aims to give devices a long life by running a real, upstream-maintained Linux system long after the manufacturer stops providing updates. The project describes this as a "ten-year life cycle" for a phone.

Instead of Android, a pmOS device boots a familiar GNU/Linux-style environment, with a touch-friendly user interface, a normal shell and a package manager.

### Key Features

#### Built on Alpine

postmarketOS uses Alpine's **apk** package manager and takes its base from Alpine's packages. Stable releases follow Alpine's own releases closely; for example, v26.06 is based on Alpine 3.24.

#### Your choice of interface

Several user interfaces are available:

- **Phosh**: a GNOME-based mobile shell.
- **Plasma Mobile**: KDE's phone interface.
- **Sxmo**: a minimalist, keyboard-and-gesture environment based on Sway.
- **GNOME Mobile** and a few others, depending on the device.

You choose one when building or installing the image.

#### Mainline-first approach

The project works to run devices on the **mainline Linux kernel** where possible, rather than outdated vendor kernels. That is hard work, device by device, but it means a fix in upstream Linux can benefit every supported phone. Support levels vary widely between devices.

#### pmbootstrap

Images are built and installed with **pmbootstrap**, a command-line tool that creates a chroot, compiles packages and flashes the device. Everything is reproducible from public sources.

#### Edge and stable releases

New stable versions arrive every six months, in June and December, a month after Alpine's own releases. Each is supported for roughly seven months. A rolling **edge** channel tracks the newest packages.

### Release 26.06

postmarketOS **v26.06 "Alpen Avocado"** was released on 21 June 2026. Reported features include GNOME 50, KDE Plasma Mobile 6.6, Phosh 0.55, a switch to Plymouth for the boot splash and support for more hardware, including some Chromebooks and a note-taking tablet. Check the release notes for your device, since results differ widely.

### Who It Is For

- **Free software enthusiasts** who want a phone that runs a real Linux system.
- **Tinkerers and developers** who like to explore device hardware.
- **People who want to keep older devices useful.**

It is not a polished daily phone for everyone. On many devices, features such as the camera, mobile data or calls are still in progress or only partly supported. Check the project's device list before choosing hardware.

### History

- **2017**: A group of developers around Oliver Smith starts the project.
- **2018-2020**: Support grows alongside the PinePhone and Librem 5, which are designed for Linux.
- **2024-2026**: Stable releases become regular and support for desktop-style and tablet devices expands.

### Comparison

| Feature | postmarketOS | Android | Ubuntu Touch |
| :--- | :--- | :--- | :--- |
| **Base** | Alpine Linux | Linux kernel with Android userspace | Ubuntu |
| **Package manager** | apk | App stores | Click packages and APT |
| **Kernel approach** | Mainline where possible | Vendor kernel | Usually vendor kernel |
| **Goal** | Long-lived real Linux phone | Mainstream phone OS | Linux phone with a ready-made UI |

### Getting Started

- **Official website**: [postmarketos.org](https://postmarketos.org)
- **Supported devices**: [wiki.postmarketos.org/wiki/Devices](https://wiki.postmarketos.org/wiki/Devices)
- **Wiki**: [wiki.postmarketos.org](https://wiki.postmarketos.org)
- **Release v26.06 notes**: [postmarketos.org/blog/2026/06/21/v26.06-release](https://postmarketos.org/blog/2026/06/21/v26.06-release/)

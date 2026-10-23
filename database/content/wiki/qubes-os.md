---
title: Qubes OS
author: Quentin Marsh
distribution: qubes
---
### Overview

**Qubes OS** is a security-focused desktop operating system built on the idea of **security by compartmentalisation**. Instead of trusting one big system to keep everything safe, Qubes runs each part of your digital life in its own isolated virtual machine, called a **qube**. A compromise in one qube, say a browser tab used for a risky site, does not reach your work documents or your password vault.

It is built on the **Xen** hypervisor, and its apps are shown together on a single desktop with coloured window borders that tell you which qube each one comes from.

### Key Features

#### Qubes and templates

- **AppVMs** are the qubes you use every day, such as "work", "personal" and "untrusted".
- **TemplateVMs** hold the operating system software that AppVMs share. Updating a template updates every qube based on it, and the root file system of an AppVM is discarded on each restart.
- **Disposable qubes** are single-use virtual machines that vanish when you close them, ideal for opening untrusted documents.
- **Standalone qubes** are full independent systems for things that do not fit the template model, including Windows.

#### Isolating the hardware

Network devices and USB controllers are moved into their own qubes (`sys-net` and `sys-usb`), so attacks through Wi-Fi drivers or malicious USB sticks stay contained. A separate `sys-firewall` qube filters traffic.

#### Admin domain

The management environment, **dom0**, has no network access and runs only the minimum required to manage the system. This keeps the most privileged part of the system away from the Internet.

#### Whonix integration

Qubes integrates with **Whonix**, which routes traffic through Tor, so anonymity and compartmentalisation can be combined.

#### Fedora and Debian inside

Qubes ships templates based on Fedora and Debian, and the community adds many more, so you work with familiar tools inside the qubes.

### Release 4.3

Qubes OS 4.3.0 was announced in December 2025. Reported changes include default templates upgraded to **Fedora 42**, **Debian 13** and **Whonix 18**, Xen 4.19 and a dom0 based on Fedora 41. Version 4.3.1 followed in June 2026 as a patch release that consolidated security updates and bug fixes.

### Requirements

Qubes needs more hardware than most distributions: a 64-bit Intel or AMD CPU with hardware virtualisation (VT-x/AMD-V) and IOMMU (VT-d/AMD-Vi), plenty of RAM (the project recommends 16 GB for comfortable use), and an SSD. Compatibility lists of tested laptops are kept by the community.

### Who It Is For

- **Journalists, researchers and activists** who handle risky files or sensitive sources.
- **Security professionals and developers** who want isolated environments for different projects.
- **Anyone who is willing to trade convenience for strong isolation.**

Everyday use is slower and more complex than on a regular system, and gaming or heavy GPU use is limited.

### History

- **2010**: Joanna Rutkowska and Invisible Things Lab start Qubes OS.
- **2012**: Qubes Release 1 is published.
- **2018**: Release 4.0 introduces the Admin API and hardware-virtualised isolation by default.
- **2025**: Release 4.3.0.

### Comparison

| Feature | Qubes OS | Tails | Regular Linux desktop |
| :--- | :--- | :--- | :--- |
| **Approach** | Isolate with virtual machines | Leave no trace, use Tor | Single shared system |
| **Persistent?** | Yes | Optional encrypted storage | Yes |
| **Hardware demands** | High | Low | Low |
| **Best for** | Daily secure computing | Anonymous temporary sessions | General use |

### Getting Started

- **Official website**: [qubes-os.org](https://www.qubes-os.org)
- **Documentation**: [doc.qubes-os.org](https://doc.qubes-os.org)
- **Hardware compatibility list**: [qubes-os.org/hcl](https://www.qubes-os.org/hcl/)
- **Release 4.3 coverage**: [Linuxiac](https://linuxiac.com/qubes-os-4-3-brings-fedora-42-debian-13-and-whonix-18-templates/)

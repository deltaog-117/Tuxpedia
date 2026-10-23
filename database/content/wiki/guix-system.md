---
title: Guix System
author: Noor Halvorsen
distribution: guix
---
### Overview

**Guix System** (formerly Guix System Distribution, or GuixSD) is an advanced Linux distribution built on **GNU Guix**, a functional package manager from the GNU Project. Like NixOS, it manages the entire operating system declaratively and transactionally. Unlike NixOS, everything is written in **GNU Guile**, a Scheme language, from the package definitions down to the init system.

It is also one of the few distributions that follows the GNU Free System Distribution Guidelines. The default kernel is **Linux-libre**, and non-free software and firmware are not included in the official channels.

### Key Features

#### One language for everything

The system configuration is a Scheme program that declares users, services, file systems, packages and the bootloader. The same language defines packages and services, so users can inspect, extend and reuse them as ordinary code.

#### Transactional upgrades and rollbacks

Packages are installed into unique directories under `/gnu/store`, identified by a hash of all their inputs. Upgrades never overwrite anything in place. If an upgrade fails or you do not like it, you can roll back to a previous system **generation**, even from the boot menu.

#### Reproducibility

The project is a leader in **reproducible builds** and the bootstrappable builds effort, which aims to build the entire system from a very small, auditable set of seeds. Package definitions pin their inputs exactly, so the same definition yields the same result elsewhere.

#### Useful on any distribution

Guix the package manager runs on top of other Linux distributions. `guix shell` creates throwaway development environments, `guix pack` produces relocatable bundles or container images and `guix time-machine` runs commands against an older state of the whole package collection.

#### Shepherd

Services are managed by **GNU Shepherd**, also written in Guile. Release 1.5 moved Guix System to Shepherd 1.0, adding timed services, kexec reboots and new logging services.

### Release 1.5

GNU Guix 1.5 was released in January 2026, three years after Guix 1.4. Reported highlights include:

- An experimental **x86_64 GNU Hurd** system as an installer option.
- Release tarballs for **RISC-V (riscv64)**.
- KDE Plasma 6.5 and GNOME on Wayland by default.
- The Guix daemon can run without root privileges.
- A plan for annual releases and a move of code hosting to Codeberg.

### Who It Is For

- **Free software purists** who want a fully free system.
- **Developers and researchers** who need reproducible environments.
- **Lisp and Scheme enthusiasts** who like a configuration language that is a real programming language.

The learning curve is steep, and the free-software policy means some hardware needs extra effort, since proprietary firmware is excluded.

### History

- **2012**: Ludovic Courtes starts GNU Guix, inspired by Nix and written in Guile.
- **2019**: Guix 1.0 is released, and the distribution formerly called GuixSD becomes known as Guix System.
- **2026**: Guix 1.5 is released.

### Comparison

| Feature | Guix System | NixOS | Debian |
| :--- | :--- | :--- | :--- |
| **Configuration language** | Scheme (Guile) | Nix language | Files and commands |
| **Init** | GNU Shepherd | systemd | systemd |
| **Software policy** | Free software only | Free by default, unfree optional | Free core, non-free optional |
| **Rollbacks** | Built in | Built in | Not built in |

### Getting Started

- **Official website**: [guix.gnu.org](https://guix.gnu.org)
- **Manual**: [guix.gnu.org/manual](https://guix.gnu.org/manual/)
- **Cookbook**: [guix.gnu.org/cookbook](https://guix.gnu.org/cookbook/)
- **Release 1.5 coverage**: [Phoronix](https://www.phoronix.com/news/GNU-Guix-1.5-Released)

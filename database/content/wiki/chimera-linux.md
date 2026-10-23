---
title: Chimera Linux
author: Priya Lindqvist
distribution: chimera
---
### Overview

**Chimera Linux** is a young, independent distribution that deliberately swaps out most of the usual GNU/Linux foundation. Instead of GCC, glibc, GNU coreutils and systemd it uses **LLVM/Clang**, **musl**, **FreeBSD userland tools** and **dinit**. The result is a general-purpose system built around the Linux kernel but assembled from very different parts, hence the name.

It is aimed at people who want a clean, modern and understandable base, with security hardening applied by default, rather than a replica of a traditional distribution. It is rolling release, and its primary developer is known as **q66**.

### Key Features

#### An LLVM-based toolchain

The whole system is compiled with **Clang** and linked with **lld**. It uses LLVM's own runtime pieces, including **compiler-rt**, **libunwind** and **libc++**, rather than their GNU counterparts. Chimera is one of the few distributions that builds almost everything this way.

#### musl with a hardened allocator

The C library is **musl**, patched to use LLVM's **Scudo** allocator. The project reports that this improves both performance and security compared with musl's default allocator.

#### FreeBSD userland

Core command-line tools such as `ls`, `cp` and `sed` come from **FreeBSD**, with some code from NetBSD and OpenBSD, instead of GNU coreutils. The developers chose them for their code quality and capabilities.

#### dinit

Services and boot are handled by **dinit**, a lightweight, dependency-based service manager and init. It is smaller than systemd while still offering supervision, parallel startup and a clean service format. Chimera pairs it with a few lightweight components to cover login and device handling.

#### apk package manager

Chimera uses **apk-tools**, the package manager also used by Alpine Linux. Packages are built with a dedicated tool, **cbuild**, in a clean, sandboxed environment, and a hardening policy such as position-independent code and stack protection applies to everything.

### Platform and Releases

Chimera supports several architectures, including x86_64, aarch64, ppc64le and riscv64. It is a rolling distribution, with periodic live and installation images. The most recent images listed at the time of writing date from December 2025. As a young project, expect rough edges: some software expecting glibc or GNU tools needs patches.

### Who It Is For

- **Developers interested in alternatives** to the GNU stack, who want to test software against LLVM and musl.
- **Hardening-minded users** who like secure defaults.
- **Enthusiasts on non-x86 hardware**, because of the broad architecture list.

It is not meant to be a first distribution, and not every application will work out of the box.

### History

- **2021**: q66 (Daniel Kolesa) starts Chimera Linux, with the first public images appearing shortly after.
- **2021-2025**: The distribution grows a larger package set and support for several desktops, including GNOME and KDE Plasma.

### Comparison

| Feature | Chimera Linux | Void Linux | Alpine Linux |
| :--- | :--- | :--- | :--- |
| **Toolchain** | LLVM/Clang | GCC | GCC |
| **C library** | musl | glibc or musl | musl |
| **Userland** | FreeBSD | GNU | BusyBox |
| **Init** | dinit | runit | OpenRC |
| **Package manager** | apk | XBPS | apk |

### Getting Started

- **Official website**: [chimera-linux.org](https://chimera-linux.org)
- **Documentation**: [chimera-linux.org/docs](https://chimera-linux.org/docs/)
- **Downloads**: [repo.chimera-linux.org/live](https://repo.chimera-linux.org/live/)
- **Packaging repository**: [github.com/chimera-linux/cports](https://github.com/chimera-linux/cports)

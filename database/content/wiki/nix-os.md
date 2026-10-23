---
title: NixOS
author: Arthur Knuth
distribution: nixos
---
### Overview

**NixOS** is a GNU/Linux distribution built on top of the **Nix package manager**. Its design differs fundamentally from traditional distributions: the entire operating system, including the kernel, applications and system services, is **declared** by writing Nix expressions, and the system is then built from that description. This gives a high degree of **reproducibility**, **reliability** and **consistency**.

The official package collection, **Nixpkgs**, is one of the largest in the Linux world, with well over 100,000 packages. NixOS publishes two stable releases a year, in May and November, named after the year and month (for example 25.05 and 25.11). It supports x86-64 and AArch64 as its main architectures.

### Core Features

#### 1. Declarative Configuration

NixOS's hallmark is **declarative configuration**. You describe the **final state** of the system in a configuration file, typically `/etc/nixos/configuration.nix`: which packages, users and services you want. Rather than running a sequence of install and configure commands, you ask the system to build exactly the environment you described. The whole configuration can be **version-controlled**, **reviewed** and **shared** like any other source code.

#### 2. Atomic Updates and Rollbacks

Updates are **atomic**. When you run `nixos-rebuild switch`, the new system is built completely before anything changes; it either fully succeeds or leaves the current system untouched. Every previous version is kept as a **generation**, listed in the boot menu. If a change breaks something, reboot into an older generation and you are back in a known-good state.

#### 3. Isolation and Reproducibility

Every package is installed under `/nix/store` in a path that includes a cryptographic **hash** of all its inputs. This means:

- **No dependency conflicts**: several versions of the same software can live side by side.
- **Reproducibility**: the same Nix expression builds the same result on any machine. The "it works on my machine" problem largely disappears.

#### 4. Two Ways to Manage Packages

- **Declarative**: list packages in `configuration.nix` and apply them with `nixos-rebuild`.
- **Ad hoc**: use `nix-shell` or `nix shell` to try a program in a temporary environment, or `nix-env` for imperative installs to a user profile, without touching the system configuration.

#### 5. Flakes and Home Manager

**Flakes** are an increasingly common way to pin a configuration's inputs in a lock file, so a whole system or project can be rebuilt exactly. They are still labelled experimental, but are used widely. **Home Manager** extends the same declarative approach to user-level settings, dotfiles and applications.

#### 6. An Unconventional Filesystem Layout

NixOS does not follow the traditional Filesystem Hierarchy Standard. There is no `/usr/lib` full of shared libraries, only `/nix/store`, plus a few compatibility links. As a result, pre-built binaries that expect standard paths need extra help to run, using tools such as `nix-ld` or `buildFHSEnv`.

### Use Cases

NixOS is particularly well suited to:

- **Developers who need reproducible environments**, so that development, testing and production match.
- **DevOps and infrastructure as code**, managing whole machines as version-controlled code.
- **People who experiment**, because any change can be undone by choosing an older generation.
- **Environments that value consistency**, such as research, CI and critical infrastructure.

**Real-world examples**:

- **Médecins Sans Frontières (MSF)** has used Nix-based tooling to manage distributed servers and virtual machines in resource-constrained environments.
- **Homelab enthusiasts** run NixOS on hypervisors such as Proxmox, declaring services like Grafana, Docker and Tailscale in a few lines.

### A Note on Asterinas NixOS

In December 2025 the Asterinas project released **Asterinas NixOS**, a distribution that combines the **Asterinas** kernel, a new operating system kernel written in Rust, with a **NixOS userland**. It is a separate project: the Asterinas team states that it is **not an official NixOS project and has no affiliation with the NixOS Foundation**. It is best seen as an experiment that shows how portable the Nix approach is.

### History

- **2003**: Eelco Dolstra starts Nix as a **research project** on reliable software deployment.
- **2006**: NixOS is first described in Armijn Hemel's **master's thesis**, which applies Nix's principles to a complete Linux distribution.
- **2015**: The **NixOS Foundation** is established in the Netherlands to support the project.
- **Today**: A large, active community maintains NixOS and Nixpkgs, and the approach has influenced other projects, including Guix System.

### Comparison with Other Distributions

| Feature | NixOS | Traditional distributions (e.g. Debian, Ubuntu, Fedora, Arch) |
| :--- | :--- | :--- |
| **Configuration model** | **Declarative**: define the final state in a config file | **Imperative**: build the system step by step with commands |
| **Package storage** | **Isolated**: each package has its own path under `/nix/store` | **Shared**: packages install into common directories like `/usr` and `/lib` |
| **Dependency management** | **Conflict-free**: multiple versions coexist | Version conflicts are possible |
| **Updates and rollbacks** | **Atomic**: all-or-nothing with generation rollbacks | Updates can fail partway, rollbacks vary by tool |
| **Learning curve** | **Steep**: you need the Nix language and a new mindset | **Gentle**: concepts are more familiar |
| **Disk use** | **Higher**, since old generations are kept until garbage-collected | **Lower**, usually one version of each package |

> Some commentators call NixOS a "lifelong Linux distro that rewards the willing": not as esoteric as Gentoo, but not as beginner-friendly as Ubuntu.

### Getting Started

- **Official website**: [nixos.org](https://nixos.org)
- **Tutorials and guides**: [nix.dev](https://nix.dev) offers installation guides and beginner-friendly tutorials
- **NixOS Wiki**: [wiki.nixos.org](https://wiki.nixos.org) holds community-maintained guides, examples and troubleshooting tips
- **NixOS manual**: [nixos.org/manual/nixos/stable](https://nixos.org/manual/nixos/stable/)
- **Nixpkgs manual**: [nixos.org/manual/nixpkgs/stable](https://nixos.org/manual/nixpkgs/stable/)
- **Asterinas NixOS announcement**: [discourse.nixos.org](https://discourse.nixos.org/t/asterinas-nixos-linux-alternative-with-nix-userland/73653)

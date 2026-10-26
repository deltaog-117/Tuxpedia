---
title: Bedrock Linux
author: Elio Brandt
distribution: bedrock
---
### Overview

**Bedrock Linux** is a **meta-distribution**: instead of being its own set of packages, it lets you combine the software of several other Linux distributions in a single, running system. You can install an Arch package, a [[debian|Debian]] package and a [[fedora|Fedora]] package side by side, and they all work as if they belonged on the same machine.

The project's pitch is that you should not have to choose between the stability of Debian, the freshness of Arch and the tools only another distribution provides. Bedrock sits underneath them and glues them together. It is a niche project, aimed at advanced users.

### Key Features

#### Strata

Each distribution you add lives in its own **stratum** (plural: strata), under `/bedrock/strata`. A stratum is basically one distribution's file tree, with its own package manager and libraries. Bedrock links them together with a mix of file system tricks and namespaces so that programs from different strata can find each other.

#### Fetching and running

- `brl fetch` downloads and sets up another distribution as a new stratum.
- `strat` runs a command from a chosen stratum, for example `strat arch pacman -Syu` or `strat debian apt install some-package`.
- Binaries from other strata appear in your path, so you can launch them like any other application.

#### Hijack installation

You do not usually install Bedrock from scratch. The installer **hijacks** an existing installation of a supported distribution, which becomes the first stratum, and adds Bedrock's pieces around it. This makes it easy to try on an existing system, though a backup first is wise.

#### Choice of components

You can pick which stratum provides your init system and which provides each application. If the init in one stratum is not what you want, another stratum can take over.

#### Frank about its limits

The project explicitly warns that details can change drastically between releases, that unusual setups may break and that some software tightly bound to a specific distribution will not work across strata. It also notes that Bedrock does not replace understanding the underlying systems.

### Release Status

Bedrock Linux **0.7 "Poki"**, released on 17 December 2018, is still the current major line, with the project issuing 0.7.x point releases. A 0.7.30 beta in March 2024 was withdrawn and rebuilt after the discovery of the xz backdoor (CVE-2024-3094). Check the project's news page for the latest version.

### Who It Is For

- **Experienced users** who like to experiment.
- **People who need software from several distributions** and do not want virtual machines or containers for each.
- **Curious readers** who enjoy seeing how a Linux system is assembled.

It is not recommended for people who want a simple, supported desktop.

### History

- **Early 2010s**: Daniel Thau starts Bedrock Linux as a way to combine Linux distributions.
- **2018**: The 0.7 series, "Poki", rebuilds the project around strata with a more reliable design.
- **2024**: The xz backdoor incident prompts a rebuild of the beta release.

### Comparison

| Feature | Bedrock Linux | Containers or virtual machines | Regular distribution |
| :--- | :--- | :--- | :--- |
| **Mix multiple distros** | Yes, in one system | Yes, but separate environments | No |
| **Native integration** | Strata share files and a desktop | Needs sharing configuration | Not applicable |
| **Complexity** | High | Medium | Low |
| **Support** | Small community | Wide | Wide |

### Getting Started

- **Official website**: [bedrocklinux.org](https://bedrocklinux.org)
- **Introduction and FAQ**: [bedrocklinux.org/introduction.html](https://bedrocklinux.org/introduction.html)
- **Documentation for 0.7**: [bedrocklinux.org/0.7](https://bedrocklinux.org/0.7)
- **Project news**: [bedrocklinux.org/news.html](https://bedrocklinux.org/news.html)

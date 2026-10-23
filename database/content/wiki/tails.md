---
title: Tails
author: Sunniva Okafor
distribution: tails
---
### Overview

**Tails** (The Amnesic Incognito Live System) is a portable operating system that you start from a USB stick on almost any computer. It sends all Internet traffic through the **Tor** network and leaves no trace on the computer it ran on once you shut it down. It exists to protect privacy and to circumvent censorship, and is used by journalists, activists and anyone who needs to work safely on a machine they do not trust.

Tails is based on **Debian** and uses the GNOME desktop. It is run by the Tails project, with funding from donations and grants, and it works with the Tor Project.

### Key Features

#### Amnesia by default

Tails runs from RAM and does not touch the computer's internal drives unless you explicitly ask it to. When you power off, memory is wiped, and the host computer shows no sign that Tails was there.

#### Everything goes through Tor

The system is configured so that every outgoing connection is routed through Tor. Applications that try to connect directly are blocked. **Tor Browser**, preconfigured for privacy, is included. Reported recent releases ship Tor Browser 15 based on Firefox ESR 140.

#### Persistent Storage, if you want it

You can create an **encrypted Persistent Storage** on the USB stick to keep documents, passwords, Wi-Fi settings and extra software across sessions. It is optional and off by default.

#### Built-in tools for safer work

Tails includes tools for encryption and secure communication, such as **KeePassXC** for passwords, **OnionShare** for sharing files over Tor, **Thunderbird** with OpenPGP for email, and metadata-removal tools for documents and images. It also changes the network card's MAC address by default to avoid tracking on local networks.

#### Easy to start

An installation assistant on tails.net walks you through writing the system to a USB stick. A verified download and signature checks help ensure the image is genuine.

### Releases

Tails publishes frequent updates, usually every four weeks to follow Tor Browser and Firefox ESR, with emergency releases for serious flaws. Release 7.11 appeared in August 2026 with Tor Browser 15.0.20 and a new kernel build. An emergency 7.10.1 release earlier in August fixed kernel and expat flaws. Always update before relying on it.

### Limits to be aware of

- Tails protects you from tracking and from leaving traces; it cannot protect you from malware already on the hardware, from a compromised BIOS or from your own mistakes.
- Using Tor can make your traffic stand out in some networks, and some sites block Tor.
- It is designed for **short, sensitive sessions**, not for a general-purpose daily system.

### History

- **2009**: The first public release of Tails merges the Amnesia project with Incognito, a Gentoo-based live system.
- **2013-2014**: Public attention grows after Tails features in coverage of the Snowden disclosures.
- **Today**: Tails 7.x is current, with a new release roughly every four weeks.

### Comparison

| Feature | Tails | Qubes OS | Whonix |
| :--- | :--- | :--- | :--- |
| **Runs from** | USB stick, in RAM | Installed on disk | Virtual machines |
| **Traces left** | None by default | Stored locally | Stored in the VM |
| **Tor** | Everything is routed through Tor | Optional, via Whonix qubes | Everything routed through Tor |
| **Best for** | Temporary anonymous sessions | Everyday compartmentalised work | Persistent anonymous environment |

### Getting Started

- **Official website**: [tails.net](https://tails.net)
- **Installation assistant**: [tails.net/install](https://tails.net/install/)
- **Documentation**: [tails.net/doc](https://tails.net/doc/)
- **Tor Project**: [torproject.org](https://www.torproject.org)

(() => {
  const root = document.querySelector('[data-ipv4-root]');
  if (!root) {
    return;
  }

  const calculator = root.querySelector('[data-calculator]');
  if (!calculator) {
    return;
  }

  const ipInput = calculator.querySelector('[data-ip-input]');
  const cidrInput = calculator.querySelector('[data-cidr-input]');
  const netmaskInput = calculator.querySelector('[data-netmask-input]');
  const status = calculator.querySelector('[data-status]');
  const resultFields = root.querySelectorAll('[data-field]');

  const config = {
    invalidIp: root.dataset.invalidIp || 'Invalid IPv4 address',
    invalidMask: root.dataset.invalidMask || 'Invalid prefix or netmask',
    locale: root.dataset.locale || 'en',
  };

  const numberFormat = new Intl.NumberFormat(config.locale);

  const padBinary = (value) => value.toString(2).padStart(8, '0');

  const ipToInt = (value) => {
    const parts = value.split('.').map((chunk) => Number(chunk.trim()));
    if (parts.length !== 4 || parts.some((part) => Number.isNaN(part) || part < 0 || part > 255)) {
      return null;
    }

    return parts.reduce((acc, part) => acc * 256 + part, 0);
  };

  const intToIp = (value) => {
    const normalized = value >>> 0;
    return [
      (normalized >>> 24) & 255,
      (normalized >>> 16) & 255,
      (normalized >>> 8) & 255,
      normalized & 255,
    ].join('.');
  };

  const prefixToMask = (prefix) => {
    if (!Number.isInteger(prefix) || prefix < 0 || prefix > 32) {
      return null;
    }

    const mask = prefix === 0 ? 0 : (0xffffffff << (32 - prefix)) >>> 0;
    return intToIp(mask);
  };

  const maskToPrefix = (mask) => {
    const maskInt = ipToInt(mask);
    if (maskInt === null) {
      return null;
    }

    const binary =
      padBinary((maskInt >>> 24) & 255) +
      padBinary((maskInt >>> 16) & 255) +
      padBinary((maskInt >>> 8) & 255) +
      padBinary(maskInt & 255);

    if (!/^1*0*$/.test(binary)) {
      return null;
    }

    return binary.replace(/0+/g, '').length;
  };

  const wildcardFromMask = (mask) => {
    const maskInt = ipToInt(mask);
    if (maskInt === null) {
      return null;
    }

    const wildcard = (~maskInt) >>> 0;
    return intToIp(wildcard);
  };

  const setStatus = (message = '') => {
    status.textContent = message;
    status.hidden = !message;
  };

  const setFieldValidity = (input, isValid) => {
    if (!input) {
      return;
    }

    input.classList.toggle('is-invalid', !isValid);
  };

  const resetResults = () => {
    resultFields.forEach((field) => {
      field.textContent = '-';
    });
  };

  const hydrateResults = (payload) => {
    const mapping = {
      networkBits: payload.networkBits,
      hostBits: payload.hostBits,
      network: payload.network,
      broadcast: payload.broadcast,
      netmask: payload.netmask,
      wildcard: payload.wildcard,
      totalHosts: numberFormat.format(payload.totalHosts),
      usableHosts: numberFormat.format(payload.usableHosts),
      firstHost: payload.firstHost,
      lastHost: payload.lastHost,
      subnetCount: numberFormat.format(payload.subnetCount),
    };

    resultFields.forEach((field) => {
      const key = field.dataset.field;
      field.textContent = mapping[key] ?? '-';
    });
  };

  const calculate = () => {
    const ipValue = ipInput.value.trim();
    const ipInt = ipToInt(ipValue);

    if (ipInt === null) {
      setFieldValidity(ipInput, false);
      setStatus(config.invalidIp);
      resetResults();
      return;
    }
    setFieldValidity(ipInput, true);

    const prefix = Number.parseInt(cidrInput.value, 10);
    if (!Number.isInteger(prefix) || prefix < 0 || prefix > 32) {
      setFieldValidity(cidrInput, false);
      setStatus(config.invalidMask);
      resetResults();
      return;
    }
    setFieldValidity(cidrInput, true);

    const netmaskValue = prefixToMask(prefix);
    if (!netmaskValue) {
      setFieldValidity(netmaskInput, false);
      setStatus(config.invalidMask);
      resetResults();
      return;
    }
    setFieldValidity(netmaskInput, true);

    if (netmaskInput.value.trim() !== netmaskValue) {
      netmaskInput.value = netmaskValue;
    }

    const wildcard = wildcardFromMask(netmaskValue);
    const hostBits = 32 - prefix;
    const network = (ipInt & ipToInt(netmaskValue)) >>> 0;
    const broadcast = (network | ipToInt(wildcard ?? '0.0.0.0')) >>> 0;
    const totalHosts = hostBits === 0 ? 1 : 2 ** hostBits;
    let usableHosts;
    if (prefix === 31) {
      usableHosts = 2;
    } else if (prefix === 32) {
      usableHosts = 1;
    } else {
      usableHosts = Math.max(totalHosts - 2, 0);
    }
    const firstHost = prefix >= 31 ? network : network + 1;
    const lastHost = prefix >= 31 ? broadcast : broadcast - 1;
    const subnetCount = prefix === 0 ? 1 : 2 ** prefix;

    hydrateResults({
      networkBits: prefix,
      hostBits,
      network: intToIp(network),
      broadcast: intToIp(broadcast),
      netmask: netmaskValue,
      wildcard: wildcard ?? '0.0.0.0',
      totalHosts,
      usableHosts,
      firstHost: intToIp(firstHost >>> 0),
      lastHost: intToIp(lastHost >>> 0),
      subnetCount,
    });

    setStatus('');
  };

  let debounceTimer;
  const scheduleCalculation = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(calculate, 120);
  };

  cidrInput.addEventListener('input', () => {
    const prefix = Number.parseInt(cidrInput.value, 10);
    const mask = prefixToMask(prefix);
    if (mask) {
      netmaskInput.value = mask;
    }
    scheduleCalculation();
  });

  ipInput.addEventListener('input', scheduleCalculation);

  netmaskInput.addEventListener('input', () => {
    const prefix = maskToPrefix(netmaskInput.value.trim());
    if (prefix !== null) {
      cidrInput.value = prefix;
    }
    scheduleCalculation();
  });

  netmaskInput.addEventListener('blur', () => {
    const prefix = maskToPrefix(netmaskInput.value.trim());
    if (prefix !== null) {
      cidrInput.value = prefix;
    }
    scheduleCalculation();
  });

  calculate();
})();

<?php

// Example of graph.php
//
// Used for graph key to display all the system
// keys and files on one visible space with links.
//
// To plug this file on graph key, define this
// variables in config.php like this:
//
// $graph_key = 'graph_key';
// $graph_template = 'templates/graph.php';
//

define('COLOR_BASE00', '#1A1B26'); // Background
define('COLOR_BASE01', '#16161E'); // Stats background
define('COLOR_BASE02', '#2F3549'); // Border
define('COLOR_BASE03', '#444B6A'); // Border/edge
define('COLOR_BASE05', '#A9B1D6'); // Text
define('COLOR_BASE0A', '#0DB9D7'); // Highlight border
define('COLOR_BASE0B', '#9ECE6A'); // Files group
define('COLOR_BASE0D', '#2AC3DE'); // Helpers group
define('COLOR_BASE0E', '#BB9AF7'); // Templates group
define('COLOR_BASE0F', '#FF9E64'); // Keys group
define('COLOR_BASE10', '#F7768E'); // Dynamic keys group
define('COLOR_BASE11', '#787C99'); // Cluster text

define('ANOTHER_KEYS', 0);           // All the system keys
?>

<!DOCTYPE html>
<html>

<head>
  <!-- The all relations unhidden -->
  <title>GarFax</title>
  <style>
    body,
    html {
      margin: 0;
      padding: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      background: <?= COLOR_BASE00 ?>;
    }

    #graph {
      width: 100%;
      height: 100%;
    }

    .list-item {
      padding: 6px;
      font: 12px monospace;
      color: <?= COLOR_BASE05 ?>;
      cursor: pointer;
      border-bottom: 1px solid <?= COLOR_BASE02 ?>;
      transition: background 0.2s;
      display: flex;
      align-items: center;
      gap: 8px;
      overflow: hidden;
    }

    .list-item:last-child {
      border-bottom: none;
    }

    .list-item .node-type {
      flex-shrink: 0;
      width: 16px;
      height: 16px;
      border-radius: 3px;
      text-align: center;
      line-height: 16px;
      font-size: 12px;
    }

    .list-item .node-type.another {
      background: <?= COLOR_BASE02 ?>;
      color: <?= COLOR_BASE05 ?>;
    }

    .list-item .node-type.keys {
      background: <?= COLOR_BASE0F ?>;
      color: <?= COLOR_BASE00 ?>;
    }

    .list-item .node-type.files {
      background: <?= COLOR_BASE0B ?>;
      color: <?= COLOR_BASE00 ?>;
    }

    .list-item .node-type.dynamic_keys {
      background: <?= COLOR_BASE10 ?>;
      color: <?= COLOR_BASE00 ?>;
    }

    .list-item .node-type.templates {
      background: <?= COLOR_BASE0E ?>;
      color: <?= COLOR_BASE00 ?>;
    }

    .list-item .node-type.helpers {
      background: <?= COLOR_BASE0D ?>;
      color: <?= COLOR_BASE00 ?>;
    }

    .list-item .node-type.meta {
      background: <?= COLOR_BASE11 ?>;
      color: <?= COLOR_BASE00 ?>;
    }

    .list-item .node-name {
      color: <?= COLOR_BASE05 ?>;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      flex: 1;
      min-width: 0;
    }

    .list-item .node-group {
      flex-shrink: 0;
      font-size: 12px;
      color: <?= COLOR_BASE03 ?>;
      text-transform: lowercase;
      margin-left: auto;
      padding-left: 8px;
    }

    .list-item.selected {
      background: <?= COLOR_BASE0A ?> !important;
      color: <?= COLOR_BASE00 ?> !important;
    }

    .list-item.selected .node-name,
    .list-item.selected .node-group {
      color: <?= COLOR_BASE00 ?> !important;
    }

    /* Legend container */
    .legend-container {
      position: fixed;
      top: 8px;
      right: 8px;
      z-index: 1000;
      width: 274px;
    }

    .legend-toggle {
      width: 256px;
      padding: 6px 8px;
      background: <?= COLOR_BASE01 ?>;
      border: 1px solid <?= COLOR_BASE02 ?>;
      color: <?= COLOR_BASE05 ?>;
      font: 12px monospace;
      border-radius: 6px;
      cursor: pointer;
      text-align: left;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
      transition: all 0.2s ease;
    }

    .legend-toggle:hover {
      border-color: <?= COLOR_BASE0A ?>;
    }

    .legend-toggle .arrow {
      font-size: 14px;
    }

    .legend-content {
      margin-top: 5px;
      background: <?= COLOR_BASE01 ?>;
      border: 1px solid <?= COLOR_BASE02 ?>;
      border-radius: 6px;
      max-height: 500px;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }

    .legend-content.collapsed {
      max-height: 0;
      opacity: 0;
      margin-top: 0;
      border: none;
      pointer-events: none;
    }

    .legend-stats .list-item {
      cursor: default;
    }

    .legend-stats .list-item:hover {
      background: transparent;
    }

    /* Search system */
    .search-container {
      position: fixed;
      top: 8px;
      left: 8px;
      z-index: 1000;
      width: 250px;
    }

    #search-input {
      width: 258px;
      padding: 6px 8px;
      background: <?= COLOR_BASE01 ?>;
      border: 1px solid <?= COLOR_BASE02 ?>;
      color: <?= COLOR_BASE05 ?>;
      font: 12px monospace;
      border-radius: 6px;
      outline: none;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }

    #search-input:focus {
      border-color: <?= COLOR_BASE0A ?>;
    }

    #search-input::placeholder {
      color: <?= COLOR_BASE03 ?>;
    }

    .search-results {
      position: absolute;
      top: 100%;
      left: 0;
      width: 274px;
      margin-top: 5px;
      max-height: calc(29px * 10 - 1px);
      overflow-y: auto;
      background: <?= COLOR_BASE01 ?>;
      border: 1px solid <?= COLOR_BASE02 ?>;
      border-radius: 6px;
      display: none;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    }

    .search-results.show {
      display: block;
    }

    .search-highlight {
      outline: 2px solid <?= COLOR_BASE0A ?> !important;
      outline-offset: 2px;
      transition: outline 0.2s ease;
    }

    @media screen and (max-width: 768px) {
      .legend-container {
        display: none;
      }
    }
  </style>
</head>

<body>
  <div class="legend-container">
    <div class="legend-toggle" id="legend-toggle">
      <span>Легенда</span>
    </div>
    <div class="legend-content collapsed" id="legend-content">
      <div class="legend-stats">
        <div class="list-item">
          <span class="node-type templates">T</span>
          <span class="node-name">Шаблоны</span>
          <span class="node-group"><?= count($templates) ?></span>
        </div>
        <div class="list-item">
          <span class="node-type helpers">H</span>
          <span class="node-name">Подсказки</span>
          <span class="node-group"><?= count($helpers) ?></span>
        </div>
        <div class="list-item">
          <span class="node-type keys">K</span>
          <span class="node-name">Ключи</span>
          <span class="node-group"><?= count($keys) ?></span>
        </div>
        <div class="list-item">
          <span class="node-type files">F</span>
          <span class="node-name">Файлы</span>
          <span class="node-group"><?= count(array_unique(array_values($keys))) ?></span>
        </div>
        <div class="list-item">
          <span class="node-type dynamic_keys">D</span>
          <span class="node-name">Динамические ключи</span>
          <span class="node-group"><?= count($dynamic_keys) ?></span>
        </div>
        <div class="list-item">
          <span class="node-type another">A</span>
          <span class="node-name">Другое</span>
          <span class="node-group"><?= ANOTHER_KEYS ?></span>
        </div>
        <div class="list-item">
          <span class="node-type meta">M</span>
          <span class="node-name">Мета</span>
          <span class="node-group"><?= count($meta) ?></span>
        </div>
      </div>
    </div>
  </div>

  <div class="search-container">
    <input type="text" id="search-input" placeholder="Поиск">
    <div id="search-results" class="search-results"></div>
  </div>

  <div id="graph"></div>

  <script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>

  <script>
    (function() {
      // Data from PHP
      const passwords = <?= json_encode($keys) ?>;
      const dynamicPasswords = <?= json_encode(array_keys($dynamic_keys)) ?>;
      const templates = <?= json_encode($templates) ?>;
      const helpers = <?= json_encode($helpers) ?>;
      const meta = <?= json_encode($meta) ?>;
      const another = <?= json_encode([['skip_log', $skip_log], ['debug_key', $debug_key], ['master_key', $master_key], ['graph_key', $graph_key]]) ?>;

      // Color constants
      const COLORS = {
        BASE00: '<?= COLOR_BASE00 ?>',
        BASE01: '<?= COLOR_BASE01 ?>',
        BASE02: '<?= COLOR_BASE02 ?>',
        BASE03: '<?= COLOR_BASE03 ?>',
        BASE05: '<?= COLOR_BASE05 ?>',
        BASE0A: '<?= COLOR_BASE0A ?>',
        BASE0B: '<?= COLOR_BASE0B ?>',
        BASE0D: '<?= COLOR_BASE0D ?>',
        BASE0E: '<?= COLOR_BASE0E ?>',
        BASE0F: '<?= COLOR_BASE0F ?>',
        BASE10: '<?= COLOR_BASE10 ?>',
        BASE11: '<?= COLOR_BASE11 ?>'
      };

      // Legend toggle functionality
      const legendToggle = document.getElementById('legend-toggle');
      const legendContent = document.getElementById('legend-content');

      const legendCollapsed = false;
      if (legendCollapsed) {
        legendContent.classList.add('collapsed');
        legendToggle.classList.add('collapsed');
      }

      legendToggle.addEventListener('click', () => {
        legendContent.classList.toggle('collapsed');
        legendToggle.classList.toggle('collapsed');
      });

      const basename = (path) => path.split('/').pop();
      const getUniqueValues = (obj) => [...new Set(Object.values(obj))];

      const files = getUniqueValues(passwords);
      const templateFiles = getUniqueValues(templates);

      // Graph data structures
      const nodes = [];
      const edges = [];
      const nodeSet = new Set();

      const addNode = (id, label, group, shape, title = null) => {
        if (!nodeSet.has(id)) {
          nodes.push({
            id,
            label,
            group,
            shape,
            title: title || label,
            size: 20
          });
          nodeSet.add(id);
        }
      };

      // Add nodes by type
      // Keys
      Object.keys(passwords).forEach(key =>
        addNode(`k_${key}`, key, 'keys', 'diamond')
      );

      // Another
      another.forEach(([key, value]) => {
        addNode(`a_${key}`, key, 'another', 'hexagon', value)
      });

      meta.forEach(([key, value]) => {
        if (key.startsWith('a_')) {
          if (!nodeSet.has(key)) {
            addNode(key, key.slice(2), 'another', 'hexagon', value.slice(2));
          }
        }
      });

      // Files
      files.forEach(file =>
        addNode(`f_${file}`, basename(file), 'files', 'box', file)
      );

      // Dynamic passwords
      dynamicPasswords.forEach(key =>
        addNode(`d_${key}`, key, 'dynamic_keys', 'triangle')
      );

      // Templates
      Object.keys(templates).forEach(template =>
        addNode(`t_${template}`, template, 'templates', 'triangleDown')
      );

      // Template files
      templateFiles.forEach(file =>
        addNode(`f_${file}`, basename(file), 'files', 'box', file)
      );

      // Helpers
      helpers.forEach(helper =>
        addNode(`h_${helper}`, helper, 'helpers', 'box', helper)
      );
 
      // Add edges
      // Key to file connections
      Object.entries(passwords).forEach(([key, file]) => {
        if (nodeSet.has(`k_${key}`) && nodeSet.has(`f_${file}`)) {
          edges.push({
            from: `k_${key}`,
            to: `f_${file}`
          });
        }
      });

      // Template to file connections
      Object.entries(templates).forEach(([template, file]) => {
        if (nodeSet.has(`t_${template}`) && nodeSet.has(`f_${file}`)) {
          edges.push({
            from: `t_${template}`,
            to: `f_${file}`
          });
        }
      });

      // Meta connections
      meta.forEach(([from, to]) => {
        if (to.startsWith('l_'))
          return;

        if (nodeSet.has(from) && nodeSet.has(to)) {
          edges.push({
            from,
            to
          });
        } else {
          console.warn(
            'Missing node:',
            from,
            to
          );
        }
      });

      // Graph configuration
      const container = document.getElementById('graph');
      const data = {
        nodes,
        edges
      };

      const options = {
        layout: {
          improvedLayout: false
        },
        nodes: {
          font: {
            size: 10,
            face: 'monospace',
            strokeWidth: 0
          },
          borderWidth: 1,
          color: {
            border: COLORS.BASE03,
            highlight: {
              border: COLORS.BASE0A,
              background: COLORS.BASE02
            }
          },
          shapeProperties: {
            borderRadius: 3
          }
        },
        edges: {
          color: COLORS.BASE03,
          width: 1,
          arrows: {
            to: {
              enabled: true,
              type: 'triangle',
              scaleFactor: 0.5
            }
          }
        },
        groups: {
          another: {
            color: {
              background: COLORS.BASE01,
              border: COLORS.BASE03
            },
            font: {
              color: COLORS.BASE05
            }
          },
          keys: {
            color: {
              background: COLORS.BASE0F,
              border: COLORS.BASE03
            },
            font: {
              color: COLORS.BASE05
            }
          },
          files: {
            color: {
              background: COLORS.BASE0B,
              border: COLORS.BASE03
            },
            font: {
              color: COLORS.BASE00
            }
          },
          dynamic_keys: {
            color: {
              background: COLORS.BASE10,
              border: COLORS.BASE03
            },
            font: {
              color: COLORS.BASE05
            }
          },
          templates: {
            color: {
              background: COLORS.BASE0E,
              border: COLORS.BASE03
            },
            font: {
              color: COLORS.BASE05
            }
          },
          helpers: {
            color: {
              background: COLORS.BASE0D,
              border: COLORS.BASE03
            },
            font: {
              color: COLORS.BASE00
            }
          },
          cluster: {
            color: {
              background: COLORS.BASE01,
              border: COLORS.BASE03
            },
            font: {
              color: COLORS.BASE11
            }
          }
        },
        physics: {
          enabled: true,
          solver: 'barnesHut',
          barnesHut: {
            gravitationalConstant: -2200,
            centralGravity: 0.5,
            springLength: 160,
            springConstant: 0.03,
            damping: 0.1
          },
          stabilization: {
            enabled: true,
            iterations: 50,
            fit: true
          }
        }
      };

      // Initialize graph
      const network = new vis.Network(container, data, options);

      const searchInput = document.getElementById('search-input');
      const searchResults = document.getElementById('search-results');

      // Search state variables
      let currentResults = []; // Current search results
      let selectedResultIndex = -1; // Index of selected result

      // Prepare search data
      const allNodes = nodes.map(node => ({
        id: node.id,
        label: node.label,
        group: node.group,
        type: node.id.split('_')[0],
        displayName: node.label
      }));

      // Search function
      function performSearch(query) {
        if (!query.trim() || !allNodes.length) {
          searchResults.classList.remove('show');
          currentResults = [];
          selectedResultIndex = -1;
          return [];
        }

        const searchTerm = query.toLowerCase().trim();
        currentResults = allNodes.filter(node =>
          node.label.toLowerCase().includes(searchTerm) ||
          node.id.toLowerCase().includes(searchTerm) ||
          node.group.toLowerCase().includes(searchTerm)
        ).slice(0, 50); // Limit to 50 results

        selectedResultIndex = currentResults.length > 0 ? 0 : -1; // Auto-select first
        displayResults(currentResults);
        return currentResults;
      }

      // Display search results
      function displayResults(results) {
        if (results.length === 0) {
          searchResults.innerHTML = '<div class="list-item">Ничего не найдено</div>';
          searchResults.classList.add('show');
          return;
        }

        const resultsHtml = results.map((node, index) => {
          const typeLetter = {
            'a': 'A',
            'k': 'K',
            'f': 'F',
            'd': 'D',
            't': 'T',
            'h': 'H',
          } [node.type] || '?';

          const selectedClass = index === selectedResultIndex ? ' selected' : '';

          return `
            <div class="list-item${selectedClass}" data-node-id="${node.id}" data-index="${index}">
              <span class="node-type ${node.group}">${typeLetter}</span>
              <span class="node-name">${escapeHtml(node.label)}</span>
              <span class="node-group">${node.group}</span>
            </div>
          `;
        }).join('');

        searchResults.innerHTML = resultsHtml;
        searchResults.classList.add('show');

        // Scroll to selected item
        if (selectedResultIndex >= 0) {
          const selectedElement = document.querySelector(`.list-item[data-index="${selectedResultIndex}"]`);
          if (selectedElement) {
            selectedElement.scrollIntoView({
              block: 'nearest'
            });
          }
        }

        // Add click handlers to results
        document.querySelectorAll('.list-item').forEach(item => {
          item.addEventListener('click', () => {
            const nodeId = item.dataset.nodeId;
            const index = parseInt(item.dataset.index);
            selectAndFocusNode(nodeId, index);
            searchInput.value = '';
            searchResults.classList.remove('show');
          });
        });
      }

      // Select and focus on node
      function selectAndFocusNode(nodeId, index) {
        selectedResultIndex = index;
        highlightAndFocusNode(nodeId);

        // Update highlight in the list
        updateSelectedHighlight();
      }

      // Update selected item highlight
      function updateSelectedHighlight() {
        document.querySelectorAll('.list-item').forEach(item => {
          item.classList.remove('selected');
        });

        const selectedElement = document.querySelector(`.list-item[data-index="${selectedResultIndex}"]`);
        if (selectedElement) {
          selectedElement.classList.add('selected');
          selectedElement.scrollIntoView({
            block: 'nearest'
          });
        }
      }

      // Navigate through results
      function navigateResults(direction) {
        if (currentResults.length === 0) return;

        if (direction === 'next') {
          selectedResultIndex = (selectedResultIndex + 1) % currentResults.length;
        } else if (direction === 'prev') {
          selectedResultIndex = selectedResultIndex - 1;
          if (selectedResultIndex < 0) {
            selectedResultIndex = currentResults.length - 1;
          }
        }

        // Update highlight
        updateSelectedHighlight();
      }

      // Highlight node and focus on it
      function highlightAndFocusNode(nodeId, withAnimation = true) {
        if (!network) return;

        try {
          // Focus on the node
          network.focus(nodeId, {
            scale: 1.5,
            animation: withAnimation ? {
              duration: 500,
              easingFunction: 'easeInOutQuad'
            } : false
          });

          // Select the node
          network.selectNodes([nodeId]);
        } catch (e) {
          console.error('Error focusing node:', e);
        }
      }

      // Debounce search input
      let searchTimeout;
      searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
          performSearch(searchInput.value);
        }, 300);
      });

      // Keyboard navigation
      searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          searchResults.classList.remove('show');
          searchInput.value = '';
          currentResults = [];
          selectedResultIndex = -1;
        }

        // Arrow down navigation
        if (e.key === 'ArrowDown') {
          e.preventDefault(); // Prevent page scroll
          if (!searchResults.classList.contains('show')) {
            // If results hidden, show them with current search
            performSearch(searchInput.value);
          } else {
            navigateResults('next');
          }
        }

        // Arrow up navigation
        if (e.key === 'ArrowUp') {
          e.preventDefault();
          if (searchResults.classList.contains('show')) {
            navigateResults('prev');
          }
        }

        // Ctrl+N / Ctrl+P navigation
        if ((e.ctrlKey || e.metaKey) && (e.key === 'n' || e.key === 'т')) {
          e.preventDefault();
          if (!searchResults.classList.contains('show')) {
            performSearch(searchInput.value);
          } else {
            navigateResults('next');
          }
        }

        if ((e.ctrlKey || e.metaKey) && (e.key === 'p' || e.key === 'з')) {
          e.preventDefault();
          if (searchResults.classList.contains('show')) {
            navigateResults('prev');
          }
        }

        // Enter - focus on selected node
        if (e.key === 'Enter' && searchResults.classList.contains('show') && selectedResultIndex >= 0) {
          e.preventDefault();
          const selectedNode = currentResults[selectedResultIndex];
          if (selectedNode) {
            selectAndFocusNode(selectedNode.id, selectedResultIndex);
            searchInput.value = '';
            searchResults.classList.remove('show');
          }
        }
      });

      // Mouse hover support
      searchResults.addEventListener('mouseover', (e) => {
        const item = e.target.closest('.list-item');
        if (item && item.dataset.index) {
          const index = parseInt(item.dataset.index);
          if (index !== selectedResultIndex) {
            selectedResultIndex = index;
            updateSelectedHighlight();
          }
        }
      });

      // Close when clicking outside
      document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
          searchResults.classList.remove('show');
        }
      });

      // Helper function to escape HTML
      function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
      }

      // window.graphNetwork = network;
    })();
  </script>
</body>

</html>
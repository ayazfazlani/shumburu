# Comprehensive ERP Implementation Plan (Factory Edition)

This plan outlines the full lifecycle of a product from initial Sales Demand to physical Dispatch, ensuring strict departmental data isolation and integrated triggers.

## 1. System Architecture: The "Golden Thread" of Data

The system operates on a linear data flow where each department "consumes" the output of the previous one:
**Sales Order (SO)** → **MRP (Planning)** → **Prod Order (PO)** → **FG Stock (Warehouse)** → **Quality (QC)** → **Dispatch**

### New Core Data Structures
| Table | Department Owner | Purpose |
| :--- | :--- | :--- |
| `fg_stocks` | Warehouse | Tracks **physical** stock on the shelf. (Available vs Reserved). |
| `stock_reservations` | Sales/Planning | Links a specific Sales Order line to a specific batch in `fg_stocks`. |
| `demand_requests` | Planning | Lists what we *need* to make because stock is missing. |
| `purchase_requisitions`| Planning | Alert to Finance/Procurement that raw material is low. |

---

## 2. Detailed Workflows & Scenarios

### Scenario: Multi-Product Sales Order
When **Sales** creates an order for Pipes A (50), B (30), and C (20):
1. **System Check**: Instantly checks `fg_stocks`.
2. **Auto-Allocation**:
   - Pipe A (Stock: 100) -> **Reserve 50** -> Order Status: "Ready".
   - Pipe B (Stock: 10) -> **Reserve 10, Demand 20** -> Order Status: "Partial".
   - Pipe C (Stock: 0) -> **Demand 20** -> Order Status: "Pending Production".
3. **Trigger**: Planning department gets a notification for the **Demands** (20 of B, 20 of C).

### Scenario: The Warehouse "Accept" Flow
1. **Production** finishes a batch. They record consumption and output.
2. **Warehouse** sees a "Pending Receipt" in their dashboard.
3. They click **[Accept into Stock]**. This creates the `fg_stocks` entry.
4. **Status**: Initially `status = 'QC_Pending'`. Stock is visible but **locked** for dispatch.

### Scenario: Quality (QC) Control
1. **Quality** inspects the new batch.
2. They mark as `QC_Passed`.
3. **Trigger**: `fg_stocks` status changes to `Available`.
4. **Auto-Fill**: The system checks if there are "Demands" for this product. If yes, it automatically creates a **Reservation** for the oldest pending Sales Order.

---

## 3. Mandatory Rules (System Controls)
- **Warehouse** cannot delete production logs (keeps reports accurate).
- **Dispatch** cannot ship items unless `fg_stock.status == 'Available'` AND it is linked to an SO.
- **Planning** cannot start production unless Raw Material is `Available`.

---

## 4. Phased Implementation Roadmap

### Phase 1: Warehouse Core & FG Tracking (CURRENT TASK)
- **Goal**: Stop report breakage and enable accurate stock views.
- **Deliverables**:
    - [NEW] `fg_stock` table migration and model.
    - [MODIFY] Production Entry to "Send to Warehouse" instead of just saving.
    - [NEW] Warehouse "Receipts" screen to accept goods into stock.
    - [NEW] "FG Stock" Overview screen for Sales/Finance to view real-time inventory.

### Phase 2: Live Dispatch & Order Integration
- **Goal**: Connect Sales Orders to physical stock.
- **Deliverables**:
    - [MODIFY] Dispatch screen to subtract from `fg_stock`.
    - [NEW] "Stock Reservation" logic (Sales blocks available stock).

### Phase 3: Demand Planning & MRP
- **Goal**: Automate the "Missing Stock" workflow.
- **Deliverables**:
    - [NEW] Demand Request system.
    - [NEW] Planning dashboard (aggregates demands by product type).

### Phase 4: Procurement & Raw Materials
- **Goal**: Complete the loop with suppliers.
- **Deliverables**:
    - [NEW] Purchase Requisitions triggered by production planning.
    - [NEW] Raw Material Stock-In (GRN) flow.

---

## 5. Phase 1 Task List (Immediate Workspace Actions)

- `[ ]` **Migration**: Create `fg_stock` (id, product_id, batch, quantity, status, location).
- `[ ]` **Model**: Define `FgStock` with relationships to Products and Batches.
- `[ ]` **Production Hook**: Update `Production.php` to increment `fg_stock` quantity when production is committed.
- `[ ]` **Dispatch Hook**: Update `OrdersOverview.php` to decrement `fg_stock` when marked as 'delivered'.
- `[ ]` **Sidebar**: Add "FG Stock Tracking" link to Warehouse menu.
- `[ ]` **UI**: Create `StockOverview` Livewire component to show current balances.

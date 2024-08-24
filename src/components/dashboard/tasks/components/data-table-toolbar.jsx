import React from "react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { DataTableViewOptions } from "@/components/dashboard/tasks/components/data-table-view-options" 
import { X } from "lucide-react"
import { DataTableFacetedFilter } from "./data-table-faceted-filter"

export function DataTableToolbar({
  table, filenameOptions,
}) {
  const isFiltered = table.getState().columnFilters.length > 0

  return (
    <div className="flex items-center justify-between">
      <div className="flex items-center flex-1 space-x-2">
        <Input
          placeholder="Filtrar templates..."
          value={(table.getColumn("filename")?.getFilterValue()) ?? ""}
          onChange={(event) =>
            table.getColumn("filename")?.setFilterValue(event.target.value)
          }
          className="h-8 w-[150px] lg:w-[250px]"
        /> 
        {table.getColumn("category_name") && (
          <DataTableFacetedFilter
            column={table.getColumn("category_name")}
            title="Categorias"
            options={filenameOptions}
          />
        )}
        {/* {table.getColumn("category_name") && (
          <DataTableFacetedFilter
            column={table.getColumn("category_name")}
            title="Categoria"
            options={priorities}
          />
        )} */}
        {isFiltered && (
          <Button
            variant="ghost"
            onClick={() => table.resetColumnFilters()}
            className="h-8 px-2 lg:px-3"
          >
            Reiniciar
            <X className="w-4 h-4 ml-2" />
          </Button>
        )}
      </div>
      <DataTableViewOptions table={table} />
    </div>
  )
}

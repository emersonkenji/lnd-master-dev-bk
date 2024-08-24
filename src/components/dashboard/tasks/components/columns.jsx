
import React, { useState } from "react"
import { ColumnDef } from "@tanstack/react-table"

import { Badge } from "@/components/ui/badge"
import { Checkbox } from "@/components/ui/checkbox"

import { labels, priorities, statuses } from "../data/data"
import { Task } from "../data/schema"
import { DataTableColumnHeader } from "./data-table-column-header"
import { DataTableRowActions } from "./data-table-row-actions"
import { Expand, X } from 'lucide-react';
import { Button } from "@/components/ui/button"




export const columns = ({ user }) =>{
  console.log(user);
  
return ([
  //checkBox
  // {
  //   id: "select",
  //   header: ({ table }) => (
  //     <Checkbox
  //       checked={
  //         table.getIsAllPageRowsSelected() ||
  //         (table.getIsSomePageRowsSelected() && "indeterminate")
  //       }
  //       onCheckedChange={(value) => table.toggleAllPageRowsSelected(!!value)}
  //       aria-label="Select all"
  //       className="translate-y-[2px]"
  //     />
  //   ),
  //   cell: ({ row }) => (
  //     <Checkbox
  //       checked={row.getIsSelected()}
  //       onCheckedChange={(value) => row.toggleSelected(!!value)}
  //       aria-label="Select row"
  //       className="translate-y-[2px]"
  //     />
  //   ),
  //   enableSorting: false,
  //   enableHiding: false,
  // },
  // //ID
  // {
  //   accessorKey: "id",
  //   header: ({ column }) => (
  //     <DataTableColumnHeader column={column} title="ID" />
  //   ),
  //   cell: ({ row }) => <div className="w-[80px]">{row.getValue("id")}</div>,
  //   enableHiding: false,
  //   enableSorting: false,
  // },
  //imagens
  {
    accessorKey: "img",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Imagem" />
    ),
    cell: ({ row }) => {
      // const label = labels.find((label) => label.value === row.original.label);
      const [isOpen, setIsOpen] = useState(false);
      const handleClose = () => {
        setIsOpen(false);
      };
      return (
        <>
          <div
            className="relative flex w-32 overflow-hidden cursor-pointer group"
            onClick={() => {
              setIsOpen(true);
            }}
          >
            <img 
              src={row.getValue("img")}
              alt=""
              oading="lazy"
              className="object-cover w-screen rounded-md aspect-auto group-hover:scale-105 h-[80px]"
            />

            <div className="absolute inset-0 flex items-center justify-center transition-opacity opacity-0 bg-black/50 group-hover:opacity-100">
              <Expand className="w-8 h-8 text-white"  />
            </div>
          </div>
          {isOpen && (
            <div className="fixed inset-0 z-50 flex items-center justify-center overflow-hidden bg-black/80">
              <div className="relative w-full h-full max-w-[90vw] max-h-[90vh] m-4 overflow-auto">
                <img
                  src={row.getValue("img")}
                  alt={`Image`}
                  // width={1200}
                  // height={800}
                  className="object-contain w-auto h-auto max-w-full "
                />
              </div>
              <div className="absolute top-4 right-4">
                  <Button
                    variant="ghost"
                    size="sm"
                    className="text-white bg-gray-900/50 hover:bg-gray-900/75"
                    onClick={handleClose}
                  >
                    <X className="w-5 h-5" />
                  </Button>
                </div>
            </div>
          )}
        </>
      );
    },
  },

  {
    accessorKey: "filename", 
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Title" />
    ),
    cell: ({ row }) => {
      // const category_id = labels.find((category_id) => category_id.value === row.original.category_id);

      return (
        <div className="flex space-x-2">
          {row.getValue("category_name") && <Badge variant="outline">{row.getValue("category_name")}</Badge>}
          <span className="max-w-[500px] truncate font-medium">
            {row.getValue("filename")}
          </span>
        </div>
      );
    },
  },

  {
    accessorKey: "category_name",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Categoria" />
    ),
    cell: ({ row }) => {
      if (!row.getValue("category_name")) {
        return null;
      }

      return (
        <div className="flex w-[100px] items-center">
          <span>{row.getValue("category_name")}</span>
        </div>
      );
    },
    filterFn: (row, id, value) => {
      return value.includes(row.getValue(id));
    },
  },
  {
    accessorKey: "created",
    header: ({ column }) => (
      <DataTableColumnHeader column={column} title="Criado em" />
    ),
    cell: ({ row }) => {
      const dataString = row.getValue("created");
      const [dataPart, horaPart] = dataString.split(" ");
      const [ano, mes, dia] = dataPart.split("-");
      const dataFormatada = `${dia}/${mes}/${ano}`;
      return (
        <div className="flex items-center">
          <span>{dataFormatada}</span>
        </div>
      );
    },
    filterFn: (row, id, value) => {
      return value.includes(row.getValue(id));
    },
  },
  {
    id: "actions",
    cell: ({ row }) => <DataTableRowActions row={row} user={user} />,
  },
])};

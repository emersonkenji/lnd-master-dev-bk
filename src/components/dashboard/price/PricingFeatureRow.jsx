import React from "react";
import { __ } from "@wordpress/i18n";
import { TableCell, TableRow } from "@/components/ui/table";
import { CheckIcon, MinusIcon } from "lucide-react";

export default function PricingFeatureRow({ feature }) {
  return (
    <TableRow key={feature.name} className="text-muted-foreground">
      <TableCell>{__(feature.name, "lnd-master-dev")}</TableCell>
      <TableCell>
        <div className="mx-auto w-min">
          {feature.free ? (
            <CheckIcon className="w-5 h-5" />
          ) : (
            <MinusIcon className="w-5 h-5" />
          )}
        </div>
      </TableCell>
      <TableCell>
        <div className="mx-auto w-min">
          {feature.startup ? (
            <CheckIcon className="w-5 h-5" />
          ) : (
            <MinusIcon className="w-5 h-5" />
          )}
        </div>
      </TableCell>
      <TableCell>
        <div className="mx-auto w-min">
          {feature.team ? (
            <CheckIcon className="w-5 h-5" />
          ) : (
            <MinusIcon className="w-5 h-5" />
          )}
        </div>
      </TableCell>
      <TableCell>
        <div className="mx-auto w-min">
          {feature.enterprise ? (
            <CheckIcon className="w-5 h-5" />
          ) : (
            <MinusIcon className="w-5 h-5" />
          )}
        </div>
      </TableCell>
    </TableRow>
  );
}
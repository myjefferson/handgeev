// resources/js/Components/Workspace/Settings/OverviewTab.jsx
import React, { useState } from 'react';
import { useForm } from '@inertiajs/react';
import QuickActionsCard from './QuickActionsCard';
import StatisticsCards from './StatisticsCards';
import WorkspaceInfoForm from './WorkspaceInfoForm';
import StatusCard from './StatusCard';
import PlanLimitsCard from './PlanLimitsCard';

export default function OverviewTab({ workspace, onOpenDuplicate, onOpenDelete }) {
    const [showMergeWarning, setShowMergeWarning] = useState(false);

    return (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div className="lg:col-span-2 space-y-8">
                <StatisticsCards workspace={workspace} />
                
                <WorkspaceInfoForm 
                    workspace={workspace}
                    showMergeWarning={showMergeWarning}
                    onMergeWarningChange={setShowMergeWarning}
                />
            </div>

            <div className="space-y-8">
                <QuickActionsCard 
                    workspace={workspace}
                    onDuplicate={onOpenDuplicate}
                    onDelete={onOpenDelete}
                />
                
                <StatusCard workspace={workspace} />
                
                <PlanLimitsCard workspace={workspace} />
            </div>
        </div>
    );
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFinancial extends Model
{
    protected $table = 'project_financials';
    protected $guarded = ['id'];

    public function project() {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Net Profits. Perusahaan, Penyusutan, Kas Divisi, Team
     */
    public function netProfits() {
        return $this->hasMany(ProjectNetProfit::class, 'project_financial_id');
    }

    /**
     * Biaya lain-lain
     */
    public function otherCosts() {
        return $this->hasMany(ProjectOtherCost::class, 'project_financial_id');
    }
}

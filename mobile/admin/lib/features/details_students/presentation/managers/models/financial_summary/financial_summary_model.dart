import '/features/details_students/presentation/managers/models/financial_summary/enrollment_contract_model.dart';
import '/features/details_students/presentation/managers/models/financial_summary/payment_model.dart';
import '/features/details_students/presentation/managers/models/financial_summary/pending_installment_model.dart';

class FinancialSummaryModel {
  final EnrollmentContractModel? enrollmentContractModel;
  final List<PaymentModel>? listOfPaymentModel;
  final List<PendingInstallmentModel>? listOfPendingInstallmentModel;
  FinancialSummaryModel({
    required this.enrollmentContractModel,
    required this.listOfPaymentModel,
    required this.listOfPendingInstallmentModel,
  });
  factory FinancialSummaryModel.fromJson({required Map<String, dynamic> json}) {
    return FinancialSummaryModel(
      enrollmentContractModel: json['enrollment_contract'] != null
          ? EnrollmentContractModel.fromJson(json: json['enrollment_contract'])
          : null,
      listOfPaymentModel: json['payments'] != null
          ? (json['payments'] as List)
                .map((e) => PaymentModel.fromJson(json: e))
                .toList()
          : null,
      listOfPendingInstallmentModel: json['pending_installments'] != null
          ? (json['pending_installments'] as List)
                .map((e) => PendingInstallmentModel.fromJson(json: e))
                .toList()
          : null,
    );
  }
}

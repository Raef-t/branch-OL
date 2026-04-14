import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/components/text_normal10_component.dart';
import '/core/styles/colors_style.dart';
import '/features/home/presentation/managers/cubits/institute_branch/institute_branch_cubit.dart';
import '/features/home/presentation/managers/models/institute_branch/institute_branch_model.dart';

class CustomPopupMenuSuccessStateInHomeView extends StatelessWidget {
  const CustomPopupMenuSuccessStateInHomeView({
    super.key,
    required this.listOfInstituteBranchModel,
    this.selectedInstituteBranchModel,
    required this.child,
  });
  final List<InstituteBranchModel> listOfInstituteBranchModel;
  final InstituteBranchModel? selectedInstituteBranchModel;
  final Widget child;
  @override
  Widget build(BuildContext context) {
    return PopupMenuButton<InstituteBranchModel>(
      offset: const Offset(45, 35),
      itemBuilder: (context) => listOfInstituteBranchModel
          .map(
            (branch) => PopupMenuItem<InstituteBranchModel>(
              value: branch,
              child: TextNormal10Component(
                text: branch.name ?? '',
                color: branch == selectedInstituteBranchModel
                    ? ColorsStyle.mediumRussetColor2
                    : ColorsStyle.mediumBrownColor,
              ),
            ),
          )
          .toList(),
      onSelected: (value) =>
          context.read<InstituteBranchCubit>().selectBranch(value),
      child: child,
    );
  }
}
